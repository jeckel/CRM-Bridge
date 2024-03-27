<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 17:05
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Adapter;

use PhpImap\Mailbox;

class ImapMailboxProvider
{
    /** @var array<string, Mailbox> */
    private array $mailboxInstances = [];

    public function getMailbox(string $imapPath, string $login, string $password): Mailbox
    {
        $key = sprintf('%s@%s', $login, $imapPath);
        if (!isset($this->mailboxInstances[$key])) {
            $this->mailboxInstances[$key] = new Mailbox(
                imapPath: $imapPath,
                login: $login,
                password: $password,
                serverEncoding: 'UTF-8'
            );
        }
        return $this->mailboxInstances[$key];
    }
}
