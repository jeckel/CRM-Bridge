<?php
/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:01
 */

namespace App\Component\WebMail\Application\Port;

use App\Component\Shared\Identity\ImapAccountId;
use App\Component\Shared\Identity\ImapMailboxId;
use App\Component\WebMail\Domain\Entity\ImapAccount;
use App\Component\WebMail\Domain\Entity\ImapMail;
use App\Component\WebMail\Domain\Entity\ImapMailbox;

interface RepositoryPort
{
    public function getAccountById(ImapAccountId $accountId): ImapAccount;

    public function getMailboxById(ImapMailboxId $mailboxId): ImapMailbox;

    public function findMailByUniqueMessageId(string $messageUniqueId): ?ImapMail;

    public function persist(ImapMailbox|ImapAccount|ImapMail $entity): void;
}
