<?php

namespace App\Component\WebMail\Domain\Entity;

use App\Component\Shared\Identity\ImapMailboxId;
use App\Component\WebMail\Application\Event\ImapMailboxCreated;
use DateTimeImmutable;
use InvalidArgumentException;

use JeckelLab\Contract\Domain\Entity\DomainEventAwareInterface;

use JeckelLab\Contract\Domain\Entity\DomainEventAwareTrait;

use function App\slug;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class ImapMailbox implements DomainEventAwareInterface
{
    use DomainEventAwareTrait;

    private string $name;
    private string $slug;   /** @phpstan-ignore-line  */
    private ?int $lastSyncUid = null; /** @phpstan-ignore-line  */
    private int $flags = 0; /** @phpstan-ignore-line  */
    private int $messages = 0; /** @phpstan-ignore-line  */
    private int $recent = 0; /** @phpstan-ignore-line  */
    private int $unseen = 0; /** @phpstan-ignore-line  */
    private int $uidNext = 0; /** @phpstan-ignore-line  */
    private ?int $uidValidity = null; /** @phpstan-ignore-line  */
    private ?DateTimeImmutable $lastSyncDate = null; /** @phpstan-ignore-line  */
    private bool $enabled = true; /** @phpstan-ignore-line  */

    public function __construct(
        public readonly ImapMailboxId $id,
        public readonly string $imapPath,
        private readonly ImapAccount $account /** @phpstan-ignore-line  */
    ) {
        $this->name = self::shortPath($imapPath);
        $this->slug = slug($this->name);
    }

    public static function new(string $imapPath, ImapAccount $account): self
    {
        $mailbox = new self(ImapMailboxId::new(), $imapPath, $account);
        $mailbox->addDomainEvent(new ImapMailboxCreated($mailbox->id));
        return $mailbox;
    }

    private static function shortPath(string $imapPath): string
    {
        $pos = strpos($imapPath, '}');
        if (false === $pos) {
            throw new InvalidArgumentException('Invalid imap path');
        }
        return substr($imapPath, $pos + 1);
    }
}
