<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap\Mail;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Cache\CacheInterface;

readonly class MailProvider
{
    public function __construct(
        private ImapMessageRepository $repository,
        private CacheInterface $imapMailCache,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function getMail(
        ImapAccount $account,
        string $folder,
        int $uid
    ): MailProxy {
        return new MailProxy(
            repository: $this->repository,
            imapMailCache: $this->imapMailCache,
            eventDispatcher: $this->eventDispatcher,
            account: $account,
            folder: $folder,
            uid: $uid
        );
    }
}
