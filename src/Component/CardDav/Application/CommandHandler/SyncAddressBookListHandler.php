<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\CommandHandler;

use App\Component\CardDav\Application\Command\SyncAddressBookList;
use App\Component\CardDav\Application\Port\RepositoryPort;
use App\Component\CardDav\Application\Service\AddressBookManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SyncAddressBookListHandler
{
    public function __construct(
        private AddressBookManager $addressBookManager,
        private RepositoryPort $repository,
    ) {}

    public function __invoke(SyncAddressBookList $command): void
    {
        $account = $this->repository->getAccountById($command->accountId);
        $this->addressBookManager->fetchAddressBookFromAccount($account);
        $this->repository->flush();
    }
}
