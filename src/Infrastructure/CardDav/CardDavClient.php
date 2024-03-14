<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use Exception;
use MStilkerich\CardDavClient\Account;
use Sabre\VObject\Document;
use Sabre\VObject\Reader;

readonly class CardDavClient
{
    public function __construct(private Account $account) {}

    /**
     * @throws Exception
     */
    public function getVCard(string $vCardUri): Document
    {
        $response = $this->account->getClient(
            $this->account->getDiscoveryUri()
        )->getAddressObject($vCardUri);
        return Reader::read($response["vcf"]);
    }
}
