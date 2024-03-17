<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Scheduler;

use App\Component\ContactManagment\Application\Command\SyncCardDavAddressBook;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncAddressBookScheduler
{
    public function __construct(
        private CardDavAddressBookRepository $addressBookRepository,
        private MessageBusInterface $messageBus
    ) {}

    public function onSchedule(): void
    {
        $addressBooks = $this->addressBookRepository->findBy(['enabled' => true]);
        foreach ($addressBooks as $addressBookEntity) {
            $this->messageBus->dispatch(
                new SyncCardDavAddressBook($addressBookEntity->getId())
            );
        }
    }
}
