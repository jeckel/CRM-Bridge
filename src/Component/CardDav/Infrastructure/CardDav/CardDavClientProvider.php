<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Infrastructure\CardDav;

use App\Component\CardDav\Domain\Entity\CardDavAccount;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\Config;
use Psr\Log\LoggerInterface;

class CardDavClientProvider
{
    /**
     * @var array<string, CardDavClient>
     */
    protected array $clientInstances = [];

    public function __construct(private readonly LoggerInterface $logger) {}

    public function getClient(CardDavAccount $cardDavAccount): CardDavClient
    {
        if (! isset($this->clientInstances[$cardDavAccount->uri()])) {
            $this->clientInstances[$cardDavAccount->uri()] = $this->createClient($cardDavAccount);
        }
        return $this->clientInstances[$cardDavAccount->uri()];
    }

    private function createClient(CardDavAccount $cardDavAccount): CardDavClient
    {
        Config::init($this->logger, $this->logger);
        return new CardDavClient(
            account: new Account(
                discoveryUri: $cardDavAccount->uri(),
                httpOptions: [
                    "username" => $cardDavAccount->login(),
                    "password" => $cardDavAccount->password()
                ]
            ),
        );
    }
}
