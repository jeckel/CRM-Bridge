<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\Config;
use Psr\Log\LoggerInterface;

class CardDavClientFactory
{
    public static function getAccount(
        ConfigurationService $configurationService,
        LoggerInterface $logger
    ): Account {
        Config::init($logger, $logger);
        return new Account(
            discoveryUri: $configurationService->get(ConfigurationKey::CARDDAV_URI) ?? '',
            httpOptions: [
                "username" => $configurationService->get(ConfigurationKey::CARDDAV_USERNAME) ?? '',
                "password" => $configurationService->get(ConfigurationKey::CARDDAV_PASSWORD) ?? ''
            ]
        );
    }
}
