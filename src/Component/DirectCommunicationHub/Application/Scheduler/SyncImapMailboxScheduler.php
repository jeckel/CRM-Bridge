<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Scheduler;

use App\Component\DirectCommunicationHub\Application\Command\SyncImapAccount;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

use function App\slug;

#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncImapMailboxScheduler
{
    public function __construct(
        private ImapAccountRepository $accountRepo,
        private MessageBusInterface $messageBus
    ) {}

    public function onSchedule(): void
    {
        foreach($this->accountRepo->findAll() as $imapAccount) {
            $this->messageBus->dispatch(new SyncImapAccount($imapAccount->getIdentity()));
        }
    }
}
