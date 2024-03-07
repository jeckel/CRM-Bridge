<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap\Mail;

use App\Component\Shared\Event\NewIncomingEmail;
use App\Component\Shared\Identity\MailId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\ImapAccount;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use App\Infrastructure\Imap\ImapMailboxConnector;
use DateTimeImmutable;
use LogicException;
use Override;
use PhpImap\IncomingMail;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;
use Symfony\Contracts\Cache\CacheInterface;

use Symfony\Contracts\Cache\ItemInterface;

use ZBateson\MailMimeParser\IMessage;
use ZBateson\MailMimeParser\Message;

use function App\new_uuid;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MailProxy implements MailInterface
{
    protected ?ImapMessage $entity = null;
    protected ?ImapMailDto $imapMail = null;

    public function __construct(
        private readonly ImapMessageRepository $repository,
        private readonly CacheInterface $imapMailCache,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ImapAccount $account,
        private readonly string $folder,
        private readonly int $uid
    ) {}

    public function getImapMail(): ImapMailDto
    {
        if (null === $this->imapMail) {
            $key = sprintf('%s-%s-%d', $this->account->getId(), $this->folder, $this->uid);
            $this->imapMail = $this->imapMailCache->get(
                $key,
                fn(ItemInterface $item): ImapMailDto => $this->retrieveIncomingMail($item)
            );
        }
        return $this->imapMail;
    }

    private function retrieveIncomingMail(?ItemInterface $item = null): ImapMailDto
    {
        $item?->expiresAfter(300);
        $mailbox = ImapMailboxConnector::fromImapAccount($this->account);
        $mail = $mailbox->getMail($this->uid, $this->folder);
        if (null === $mail) {
            throw new LogicException('Mail not found');
        }
        return $mail;
    }

    public function getEntity(): ImapMessage
    {
        if (null === $this->entity) {
            $this->entity = $this->repository->findOneBy(
                [
                    'imapAccount' => $this->account,
                    'folder' => $this->folder,
                    'uid' => $this->uid
                ]
            );
        }
        if (null === $this->entity) {
            $mail = $this->getImapMail();
            $parsedHeaders = Message::from($mail->headersRaw ?? '', true);
            $this->entity = (new ImapMessage())
                ->setImapAccount($this->account)
                ->setFolder($this->folder)
                ->setUid($this->uid)
                ->setMessageId($mail->messageId ?? '')
                ->setDate(new DateTimeImmutable($mail->date ?? throw new LogicException('Date can not be null')))
                ->setSubject($mail->subject ?? throw new LogicException('Subject can not be null'))
                ->setFromName($mail->fromName ?? $mail->fromAddress ?? throw new LogicException('Both fromName and fromAddress can not be null'))
                ->setFromAddress($mail->fromAddress ?? throw new LogicException('fromAddress can not be null'))
                ->setToString($this->extractEmailTarget($mail, $parsedHeaders))
                ->setHeaderRaw($mail->headersRaw ?? '')
                ->setTextPlain($mail->textPlain)
//                ->setTextHtml(imap_utf8($mail->textHtml))
                ->setIsSpam($this->extractSpamStatusFromHeaders($parsedHeaders))
                ->setSpamScore($this->extractSpamScoreFromHeaders($parsedHeaders))
                ->setSpamHeaders($this->extractSpamHeadersFromHeaders($parsedHeaders))
            ;
            try {
                $this->repository->persist($this->entity);
            } catch (\Exception $e) {
                dd($this->entity, $e);
            }
            $this->eventDispatcher->dispatch(new NewIncomingEmail(
                mailId: MailId::from((string) $this->entity->getId()),
                email: new Email($this->entity->getFromAddress()),
                sendAt: $this->entity->getDate()
            ));
        }
        return $this->entity;
    }

    private function extractEmailTarget(ImapMailDto $mail, IMessage $parsedHeaders): string
    {
        if (null !== $mail->toString) {
            return $mail->toString;
        }
        $deliveredTo = $parsedHeaders->getHeader('Delivered-To');
        if (null !== $deliveredTo) {
            return (string) $deliveredTo->getValue();
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

    public function sync(): void
    {
        $this->getEntity();
    }

    #[Override]
    public function subject(): string
    {
        return $this->getEntity()->getSubject();
    }

    #[Override]
    public function fromName(): string
    {
        return $this->getEntity()->getFromName();
    }

    #[Override]
    public function date(): DateTimeImmutable
    {
        return $this->getEntity()->getDate();
    }
}
