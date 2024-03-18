<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Scheduler;

use App\Component\CardDav\Application\Command\SyncCardDavAddressBook;
use App\Component\CardDav\Application\Port\RepositoryPort;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncAddressBookScheduler
{
    public function __construct(
        private RepositoryPort $repository,
        private MessageBusInterface $messageBus
    ) {}

    public function onSchedule(): void
    {
        $addressBooks = $this->repository->getEnabledAddressBooks();
        foreach ($addressBooks as $addressBookEntity) {
            $this->messageBus->dispatch(
                new SyncCardDavAddressBook($addressBookEntity->id())
            );
        }
    }
}
