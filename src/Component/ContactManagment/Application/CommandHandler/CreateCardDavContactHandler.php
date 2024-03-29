<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 14/03/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\CommandHandler;

use App\Component\ContactManagment\Application\Command\CreateCardDavContact;
use App\Component\ContactManagment\Application\Event\CardDavAddressBookUpdated;
use App\Infrastructure\CardDav\CardDavClientProvider;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sabre\VObject\Component\VCard;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateCardDavContactHandler
{
    public function __construct(
        private CardDavAddressBookRepository $addressBookRepository,
        private CardDavClientProvider $clientProvider,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function __invoke(CreateCardDavContact $command): void
    {
        $data = $command->contactData;
        $vcard =  new VCard([
            'FN'  => $data->displayName,
            'N'   => [$data->lastName, $data->firstName, '', '', ''],
            'TEL' => [$data->phoneNumber],
            'EMAIL' => [$data->emailAddress?->getEmail()],
            'ORG' => [$data->company],
        ]);
        $addressBook = $this->addressBookRepository->getById($command->addressBookId);
        $abook = $this->clientProvider->getClient($addressBook->getCardDavAccountOrFail())
            ->getAddressBook($addressBook->getUri());

        $abook->createCard($vcard);

        $this->eventDispatcher->dispatch(
            new CardDavAddressBookUpdated($command->addressBookId)
        );
    }
}
