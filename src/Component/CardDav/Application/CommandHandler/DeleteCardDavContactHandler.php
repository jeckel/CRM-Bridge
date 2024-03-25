<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\CommandHandler;

use App\Component\CardDav\Application\Command\DeleteCardDavContact;
use App\Component\CardDav\Application\Event\CardDavAddressBookUpdated;
use App\Component\CardDav\Application\Port\RepositoryPort;
use App\Component\CardDav\Infrastructure\CardDav\CardDavClientProvider;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteCardDavContactHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private CardDavClientProvider $clientProvider,
        private RepositoryPort $repository
    ) {}

    /**
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function __invoke(DeleteCardDavContact $command): void
    {
        $cardDavAddressBook = $this->clientProvider->getClient($this->repository->getAccountById($command->cardDavAccountId))
            ->getAddressBook($this->repository->getAddressBookById($command->addressBookId)->getUri());

        $cardDavAddressBook->deleteCard($command->vCardUri);

        $this->eventDispatcher->dispatch(
            event: new CardDavAddressBookUpdated($command->addressBookId)
        );
    }
}
