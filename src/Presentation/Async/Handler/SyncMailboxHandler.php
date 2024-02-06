<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Handler;

use App\Infrastructure\Imap\MailboxSynchronizer;
use App\Presentation\Async\Message\SyncMailbox;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsMessageHandler]
#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncMailboxHandler
{
    public function __construct(
        private MailboxSynchronizer $mailboxSynchronizer
    ) {}

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(SyncMailbox $webhook): void
    {
        $this->mailboxSynchronizer->sync();
    }

    public function onSchedule(): void
    {
        $this->mailboxSynchronizer->sync();
    }
}
