<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

use App\Infrastructure\Doctrine\Entity\Mail;
use App\Infrastructure\Doctrine\Repository\MailRepository;
use DateTimeImmutable;
use Exception;
use PhpImap\Mailbox;
use Psr\Log\LoggerInterface;

readonly class MailboxSynchronizer
{
    public function __construct(
        private Mailbox $mailbox,
        private MailRepository $mailRepository,
        private LoggerInterface $logger
    ) {}

    /**
     * @throws Exception
     */
    public function sync(): void
    {
        $mailsIds = $this->mailbox->searchMailbox('ALL');
        foreach($mailsIds as $mailId) {
            $mail = $this->mailbox->getMail($mailId);
            if (null !== $this->mailRepository->find($mail->messageId)) {
                continue;
            }
            $doctrineMail = new Mail();
            $doctrineMail->setMessageId($mail->messageId)
                ->setDate(new DateTimeImmutable($mail->date))
                ->setSubject($mail->subject)
                ->setFromName($mail->fromName)
                ->setFromAddress($mail->fromAddress)
                ->setToString($mail->toString)
                ->setTextPlain($mail->textPlain ?? '')
                ->setTextHtml($mail->textHtml ?? '')
            ;
            $this->mailRepository->persist($doctrineMail);
            $this->logger->debug("Persisted mail: " . $mail->messageId);
        }
    }
}
