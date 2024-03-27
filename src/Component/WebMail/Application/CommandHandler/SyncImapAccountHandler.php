<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:43
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\CommandHandler;

use App\Component\WebMail\Application\Command\SyncImapAccount;
use App\Component\WebMail\Application\Port\ImapPort;
use App\Component\WebMail\Application\Port\RepositoryPort;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncImapAccountHandler
{
    public function __construct(
        private RepositoryPort $repository,
        private ImapPort $imap,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function __invoke(SyncImapAccount $command): void
    {
        $account = $this->repository->getAccountById($command->accountId);

        foreach($this->imap->listMailboxes($account) as $mailboxImapPath) {
            $account->addMailbox($mailboxImapPath);
        };
        $this->repository->persist($account);

        foreach ($account->popEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
