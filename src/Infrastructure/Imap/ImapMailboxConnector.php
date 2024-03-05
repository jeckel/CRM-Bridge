<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use App\Infrastructure\Imap\Mail\ImapMailDto;
use App\Infrastructure\Imap\Mail\ImapMailHeaderDto;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;

class ImapMailboxConnector
{
    private Mailbox $mailbox;

    public function __construct(
        private readonly string $host,
        private readonly string $login,
        private readonly string $password,
    ) {}

    public static function fromImapAccount(ImapAccount $imapConfig): ImapMailboxConnector
    {
        return new ImapMailboxConnector(
            sprintf('{%s:993/imap/ssl}INBOX', $imapConfig->getUri()),
            $imapConfig->getLogin(),
            $imapConfig->getPassword()
        );
    }

    public function getMailbox(): Mailbox
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
     * @return list<ImapMailboxDto>
     * @throws \Exception
     */
    public function listFolders(): array
    {
        return array_map(
            /** @phpstan-ignore-next-line  */
            function (string $folder): ImapMailboxDto {
                $this->getMailbox()->switchMailbox($folder);
                return new ImapMailboxDto($folder, ...get_object_vars($this->getMailbox()->statusMailbox()));
            },
            $this->getMailbox()->getListingFolders()
        );
    }

    /**
     * @return int[]
     */
    public function searchFolder(string $folder, string $criteria = 'ALL'): array
    {
        $mailbox = new Mailbox(
            str_replace('INBOX', $folder, $this->host),
            $this->login,
            $this->password
        );
        return $mailbox->searchMailbox($criteria);
    }

    public function getMail(int $id, string $folder): ImapMailDto
    {
        $mailBox = new Mailbox(
            str_replace('INBOX', $folder, $this->host),
            $this->login,
            $this->password
        );

        $mail = $mailBox->getMail($id);
        return new ImapMailDto(
            /** @phpstan-ignore-next-line */
            headers: new ImapMailHeaderDto(...get_object_vars($mail)),
            textHtml: $mail->textHtml,
            textPlain: $mail->textPlain
        );
    }
}
