<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Services\Discovery;

readonly class AddressBookDiscovery
{
    public function __construct(private Account $account) {}

    /**
     * @return array<int,AddressbookCollection>
     * @throws \Exception
     */
    public function discoverAddressBooks(): array
    {
        $discover = new Discovery();
        return $discover->discoverAddressbooks($this->account);
    }
}
