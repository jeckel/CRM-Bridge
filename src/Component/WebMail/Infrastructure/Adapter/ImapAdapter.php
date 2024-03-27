<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 12:20
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Adapter;

use App\Component\WebMail\Application\Port\ImapPort;
use App\Component\WebMail\Domain\Entity\ImapAccount;
use PhpImap\Mailbox;

readonly class ImapAdapter implements ImapPort
{
    #[\Override]
    public function listMailboxes(ImapAccount $account): array
    {
        $mailbox = new Mailbox(
            imapPath: sprintf('{%s:993/imap/ssl}INBOX', $account->uri()),
            login: $account->login(),
            password: $account->password(),
            serverEncoding: 'UTF-8'
        );

        return $mailbox->getListingFolders();
    }
}
