<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Config;
use MStilkerich\CardDavClient\Services\Discovery;
use Psr\Log\LoggerInterface;

readonly class AddressBookDiscovery
{
    public function __construct(private LoggerInterface $logger) {}
    //    public function __construct(private Account $account) {}

    /**
     * @return array<int,AddressbookCollection>
     * @throws \Exception
     */
    public function discoverAddressBooks(CardDavConfig $config): array
    {
        Config::init($this->logger, $this->logger);
        $account = new Account(
            discoveryUri: $config->getUri(),
            httpOptions: [
                "username" => $config->getLogin(),
                "password" => $config->getPassword()
            ]
        );

        $discover = new Discovery();
        return $discover->discoverAddressbooks($account);
    }
}
