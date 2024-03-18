<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\CommandHandler;

use App\Component\CardDav\Application\Command\UpdateAddressBooksActivation;
use App\Component\CardDav\Application\Port\RepositoryPort;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UpdateAddressBooksActivationHandler
{
    public function __construct(
        private RepositoryPort $repository,
    ) {}

    public function __invoke(UpdateAddressBooksActivation $command): void
    {
        $addressBookEntities = $this->repository->getAddressBooksByAccount($command->accountId);

        foreach ($addressBookEntities as $addressBook) {
            $addressBook->setEnabled(
                in_array($addressBook->id(), $command->enabledAddressBookIds, true)
            );
            $this->repository->persist($addressBook);
        }
        $this->repository->flush();
    }
}
