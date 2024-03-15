<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\CommandHandler;

use App\Component\ContactManagment\Application\Command\DeleteCardDavContact;
use App\Component\ContactManagment\Application\Event\CardDavAddressBookUpdated;
use App\Infrastructure\CardDav\CardDavClientProvider;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
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
        private ContactRepository $contactRepository
    ) {}

    /**
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function __invoke(DeleteCardDavContact $command): void
    {
        $contact = $this->contactRepository->getById($command->contactId);

        $cardDavAddressBook = $this->clientProvider->getClient($contact->getCardDavAccountOrFail())
            ->getAddressBook($contact->getAddressBookOrFail()->getUri());

        $vCardUri = $contact->getVCardUri();
        if (null !== $vCardUri) {
            $cardDavAddressBook->deleteCard($vCardUri);

            $this->eventDispatcher->dispatch(
                event: new CardDavAddressBookUpdated($contact->getAddressBookOrFail()->getIdentity())
            );
        }
    }
}
