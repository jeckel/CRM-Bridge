<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Supervisor;

use fXmlRpc\Client as XmlClient;
use fXmlRpc\Transport\PsrTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Supervisor\Supervisor;

class SupervisorFactory
{
    static public function getSupervisor(
        string $url,
        string $user,
        string $password,
    ): Supervisor
    {
        // Create Guzzle HTTP client
        $guzzleClient = new Client([
            'auth' => [$user, $password],
        ]);

        // Pass the url and the guzzle client to the fXmlRpc Client
        $client = new XmlClient(
            $url,
            new PsrTransport(
                new HttpFactory(),
                $guzzleClient
            )
        );

        return new Supervisor($client);
    }
}
