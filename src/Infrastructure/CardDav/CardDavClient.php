<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use App\Infrastructure\CardDav\VCard\ContactVCard;
use Exception;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Services\Discovery;
use RuntimeException;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\Reader;

readonly class CardDavClient
{
    public function __construct(private Account $account) {}

    /**
     * @return array<int,AddressbookCollection>
     * @throws Exception
     */
    public function discoverAddressBooks(): array
    {
        return (new Discovery())->discoverAddressbooks($this->account);
    }

    /**
     * @throws Exception
     */
    public function getAddressBook(string $addressBookUri): AddressbookCollection
    {
        $filtered = array_filter(
            $this->discoverAddressBooks(),
            fn(AddressbookCollection $collection) => $collection->getUri() === $addressBookUri
        );
        if (count($filtered) === 0) {
            throw new AddressBookNotFoundException($addressBookUri);
        }
        return array_values($filtered)[0];
    }

    /**
     * @throws Exception
     */
    public function getVCard(string $vCardUri): ContactVCard
    {
        $response = $this->account->getClient(
            $this->account->getDiscoveryUri()
        )->getAddressObject($vCardUri);
        $vCard = Reader::read($response["vcf"]);
        if (! $vCard instanceof VCard) {
            throw new RuntimeException('Could not parse vCard');
        }
        return new ContactVCard($vCardUri, $vCard);
    }
}
