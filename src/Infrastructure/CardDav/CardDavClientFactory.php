<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\Config;
use Psr\Log\LoggerInterface;

class CardDavClientFactory
{
    public static function getAccount(
        string $discoveryUri,
        string $username,
        string $password,
        LoggerInterface $logger
    ): Account {
        Config::init($logger, $logger);
        return new Account(
            discoveryUri: $discoveryUri,
            httpOptions: ["username" => $username, "password" => $password]
        );
    }
}
