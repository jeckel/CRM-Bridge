<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

use App\Infrastructure\Doctrine\Entity\ImapConfig;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;

class ImapMailbox
{
    private Mailbox $mailbox;

    public function __construct(
        private readonly string $host,
        private readonly string $login,
        private readonly string $password,
    ) {}

    public static function fromImapConfig(ImapConfig $imapConfig): ImapMailbox
    {
        return new ImapMailbox(
            sprintf('{%s:993/imap/ssl}INBOX', $imapConfig->getUri()),
            $imapConfig->getLogin(),
            $imapConfig->getPassword()
        );
    }

    private function getMailbox(): Mailbox
    {
        if (! isset($this->mailbox)) {
            $this->mailbox = new Mailbox(
                $this->host,
                $this->login,
                $this->password
            );
        }
        return $this->mailbox;
    }

    /**
     * @return array<array{shortpath: string}>
     */
    public function listFolders(): array
    {
        return $this->getMailbox()->getMailboxes('*');
    }

    public function searchFolder(string $folder, string $criteria = 'ALL'): array
    {
        $mailbox = new Mailbox(
            str_replace('INBOX', $folder, $this->host),
            $this->login,
            $this->password
        );
        return $mailbox->searchMailbox($criteria);
    }

    public function getMail(int|string $id, string $folder): IncomingMail
    {
        $mailBox = new Mailbox(
            str_replace('INBOX', $folder, $this->host),
            $this->login,
            $this->password
        );
        return $mailBox->getMail($id);
    }
}
