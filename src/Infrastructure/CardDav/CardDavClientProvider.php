<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use App\Infrastructure\Doctrine\Entity\CardDavAccount;
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
        if (! isset($this->clientInstances[$cardDavAccount->getUri()])) {
            $this->clientInstances[$cardDavAccount->getUri()] = $this->createClient($cardDavAccount);
        }
        return $this->clientInstances[$cardDavAccount->getUri()];
    }

    private function createClient(CardDavAccount $cardDavAccount): CardDavClient
    {
        Config::init($this->logger, $this->logger);
        return new CardDavClient(
            account: new Account(
                discoveryUri: $cardDavAccount->getUri(),
                httpOptions: [
                    "username" => $cardDavAccount->getLogin(),
                    "password" => $cardDavAccount->getPassword()
                ]
            ),
        );
    }
}
