<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Service;

use App\Component\Shared\Identity\CardDavAddressBookId;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class AddressBookSyncHandlerProvider
{
    public function __construct(private MessageBusInterface $messageBus) {}

    public function getSyncHandler(CardDavAddressBookId $addressBookId): AddressBookSyncHandler
    {
        return new AddressBookSyncHandler(messageBus: $this->messageBus, addressBookId: $addressBookId);
    }
}
