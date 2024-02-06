<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Services\Sync;
use MStilkerich\CardDavClient\WebDavResource;
use MStilkerich\CardDavClient\XmlElements\ElementNames;

readonly class SyncManager
{
    public function __construct(
        private AddressBookSyncHandler $syncHandler,
        private Account $account
    ) {}

    public function synchronize(string $addressBookUri): string
    {
        $syncManager = new Sync();
        /** @var AddressbookCollection $addressBook */
        $addressBook = WebDavResource::createInstance(
            uri: $addressBookUri,
            account: $this->account,
            restype: [ElementNames::RESTYPE_ABOOK]
        );
        return $syncManager->synchronize($addressBook, $this->syncHandler, [ "FN" ], "");
    }
}
