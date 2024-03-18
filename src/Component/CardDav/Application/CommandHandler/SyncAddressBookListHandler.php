<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\CommandHandler;

use App\Component\CardDav\Application\Command\SyncAddressBookList;
use App\Component\CardDav\Application\Service\AddressBookManager;
use App\Component\CardDav\Domain\Entity\CardDavAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncAddressBookListHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AddressBookManager $addressBookManager,
    ) {}

    public function __invoke(SyncAddressBookList $command): void
    {
        /** @phpstan-ignore-next-line */
        $account = $this->entityManager->getRepository(CardDavAccount::class)->getById($command->accountId);
        $this->addressBookManager->fetchAddressBookFromAccount($account);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
