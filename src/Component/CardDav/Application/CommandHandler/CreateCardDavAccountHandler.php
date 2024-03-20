<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\CommandHandler;

use App\Component\CardDav\Application\Command\CreateCardDavAccount;
use App\Component\CardDav\Application\Port\RepositoryPort;
use App\Component\CardDav\Application\Service\AddressBookManager;
use App\Component\CardDav\Domain\Entity\CardDavAccount;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateCardDavAccountHandler
{
    public function __construct(
        private RepositoryPort $repository,
        private AddressBookManager $addressBookManager,
    ) {}

    public function __invoke(CreateCardDavAccount $command): void
    {
        $account = CardDavAccount::new(
            name: $command->name,
            uri: $command->uri,
            login: $command->login,
            password: $command->password
        );
        $this->repository->persist($account);
        $this->addressBookManager->fetchAddressBookFromAccount($account);
        $this->repository->flush();
    }
}