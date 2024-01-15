<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\EspoCRM;

use Espo\ApiClient\Client;

class EspoCRMFactory
{
    public static function getEspoCRM(string $url, string $apiKey): Client
    {
        //        dd($url);
        return (new Client($url))->setApiKey($apiKey);
    }
}
