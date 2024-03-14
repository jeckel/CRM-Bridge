<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use App\Infrastructure\Doctrine\Entity\CardDavAccount;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Config;
use MStilkerich\CardDavClient\Services\Discovery;
use Psr\Log\LoggerInterface;
use Sabre\VObject\Document;
use Sabre\VObject\Reader;

readonly class AddressBookDiscovery
{
    public function __construct(private LoggerInterface $logger) {}

    /**
     * @return array<int,AddressbookCollection>
     * @throws \Exception
     */
    public function discoverAddressBooks(CardDavAccount $cardDavAccount): array
    {
        $account = $this->getAccount($cardDavAccount);

        $discover = new Discovery();
        return $discover->discoverAddressbooks($account);
    }

    public function getVCard(CardDavAccount $cardDavAccount, string $vCardUri): Document
    {
        $account = $this->getAccount($cardDavAccount);
        $response = $account->getClient($account->getDiscoveryUri())->getAddressObject($vCardUri);
        return Reader::read($response["vcf"]);
    }

    /**
     * @param CardDavAccount $config
     * @return Account
     */
    protected function getAccount(CardDavAccount $config): Account
    {
        Config::init($this->logger, $this->logger);
        $account = new Account(
            discoveryUri: $config->getUri(),
            httpOptions: [
                "username" => $config->getLogin(),
                "password" => $config->getPassword()
            ]
        );
        return $account;
    }
}
