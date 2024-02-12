<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Application\AsyncHandler;

use App\Application\Service\CardDavAddressBookSynchronizer;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use App\Presentation\Async\Message\SyncAddressBook;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsMessageHandler]
#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncAddressBookHandler
{
    public function __construct(
        private CardDavAddressBookRepository $addressBookRepository,
        private CardDavAddressBookSynchronizer $addressBookSynchronizer
    ) {}

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(SyncAddressBook $message): void
    {
        $this->addressBookSynchronizer->syncAddressBook(
            $this->addressBookRepository->getById($message->addressBookId)
        );
    }

    public function onSchedule(): void
    {
        $this->syncAddressBooks();
    }

    private function syncAddressBooks(): void
    {
        $addressBooks = $this->addressBookRepository->findBy(['enabled' => true]);
        foreach ($addressBooks as $addressBookEntity) {
            $this->addressBookSynchronizer->syncAddressBook($addressBookEntity);
        }
    }
}
