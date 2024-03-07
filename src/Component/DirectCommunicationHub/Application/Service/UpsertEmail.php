<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/03/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Service;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use App\Infrastructure\Imap\Mail\ImapMailDto;
use DateTimeImmutable;
use LogicException;
use ZBateson\MailMimeParser\IMessage;
use ZBateson\MailMimeParser\Message;

readonly class UpsertEmail
{
    public function __construct(
        private ImapMessageRepository $repository
    ) {}

    public function upsert(ImapMailDto $mail, ImapAccount $account): ?ImapMessage
    {
        $imapMessage = $this->repository->findOneBy(['imapAccount' => $account, 'messageUniqueId' => $mail->messageUniqueId]);
        if (null !== $imapMessage) {
            return $imapMessage;
        }

        $parsedHeaders = Message::from($mail->headersRaw ?? '', true);
        $imapMessage = (new ImapMessage())
            ->setImapAccount($account)
            ->setFolder($mail->imapPath)
            ->setUid($mail->uid)
            ->setMessageId($mail->messageId ?? '')
            ->setMessageUniqueId($mail->messageUniqueId)
            ->setDate(new DateTimeImmutable($mail->date ?? throw new LogicException('Date can not be null')))
            ->setSubject($mail->subject ?? throw new LogicException('Subject can not be null'))
            ->setFromName($mail->fromName ?? $mail->fromAddress ?? throw new LogicException('Both fromName and fromAddress can not be null'))
            ->setFromAddress($mail->fromAddress ?? throw new LogicException('fromAddress can not be null'))
            ->setDeliveredTo($this->extractDeliveredTo($mail, $parsedHeaders))
            ->setToString($mail->toString)
            ->setHeaderRaw($mail->headersRaw ?? '')
            ->setTextPlain($mail->textPlain)
            ->setTextHtml($mail->textHtml)
            ->setIsSpam($this->extractSpamStatusFromHeaders($parsedHeaders))
            ->setSpamScore($this->extractSpamScoreFromHeaders($parsedHeaders))
            ->setSpamHeaders($this->extractSpamHeadersFromHeaders($parsedHeaders))
            ->setHasAttachment($mail->hasAttachments)
        ;
        $this->repository->persist($imapMessage);

        return $imapMessage;
    }

    private function extractDeliveredTo(ImapMailDto $mail, IMessage $parsedHeaders): string
    {
        $deliveredTo = $parsedHeaders->getHeader('Delivered-To');
        if (null !== $deliveredTo) {
            return (string) $deliveredTo->getValue();
        }
        if (count($mail->headers->to) > 0) {
            return array_keys($mail->headers->to)[0];
        }
        throw new LogicException('Enable to extract email target from headers');
    }

    private function extractSpamStatusFromHeaders(IMessage $parsedHeaders): bool
    {
        $spamStatus = $parsedHeaders->getHeader('X-Ovh-Spam-Status');
        if (null === $spamStatus) {
            return false;
        }
        return $spamStatus->getValue() === 'SPAM';
    }

    private function extractSpamScoreFromHeaders(IMessage $parsedHeaders): int
    {
        $header = $parsedHeaders->getHeader('X-VR-SPAMSCORE');
        return (int) $header?->getValue();
    }

    /**
     * @param IMessage $parsedHeaders
     * @return array<string, string>
     */
    private function extractSpamHeadersFromHeaders(IMessage $parsedHeaders): array
    {
        $headerKeys = ['X-VR-SPAMSTATE', 'X-VR-SPAMSCORE', 'X-VR-SPAMCAUSE', 'X-Ovh-Spam-Status', 'X-Ovh-Spam-Reason', 'X-Ovh-Message-Type', 'X-Spam-Tag'];
        $spamHeaders = [];
        foreach ($headerKeys as $headerKey) {
            $header = $parsedHeaders->getHeader($headerKey);
            if (null === $header) {
                continue;
            }
            $spamHeaders[$headerKey] = $header->getValue() ?? '';
        }
        return $spamHeaders;
    }
}
