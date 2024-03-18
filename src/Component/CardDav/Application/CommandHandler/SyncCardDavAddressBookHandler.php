<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\CommandHandler;

use App\Component\CardDav\Application\Command\SyncCardDavAddressBook;
use App\Component\CardDav\Application\Service\AddressBookSynchronizer;
use App\Component\CardDav\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use App\Component\ContactManagment\Application\Service\CardDavAddressBookSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncCardDavAddressBookHandler
{
    public function __construct(
        private AddressBookSynchronizer $addressBookSynchronizer,
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(SyncCardDavAddressBook $command): void
    {
        $this->addressBookSynchronizer->sync($command->addressBookId);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
