<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:58
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\Port;


use App\Component\WebMail\Application\Dto\ImapMailDto;
use App\Component\WebMail\Application\Dto\MailboxStatusDto;
use App\Component\WebMail\Domain\Entity\ImapAccount;

interface ImapPort
{
    /**
     * @return string[]
     */
    public function listMailboxes(ImapAccount $account): array;

    public function getStatus(ImapAccount $account, string $imapPath): MailboxStatusDto;

    public function getMail(ImapAccount $account, string $imapPath, int $uid): ?ImapMailDto;
}
