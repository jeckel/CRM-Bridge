<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Calendly;

use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CalendlyClient
{
    protected ?Resource $resourceMe = null;

    public function __construct(
        private readonly string $accessToken,
        private HttpClientInterface $client
    ) {
    }

    /**
     * @return iterable<Webhook>
     */
    public function listWebhooks(): iterable
    {
        $response =  $this->client
            ->withOptions(
                (new HttpOptions())
                    ->setAuthBearer($this->accessToken)
                    ->toArray()
            )
            ->request(
                method: 'GET',
                url: 'https://api.calendly.com/webhook_subscriptions',
                options: [
                    'query' => [
                        'organization' => $this->resourceMe()->current_organization,
                        'scope' => 'organization'
                    ]
                ]
            );

        foreach ($response->toArray()['collection'] as $webhook) {
            yield new Webhook(...$webhook);
        }
    }

    public function resourceMe(): Resource
    {
        if ($this->resourceMe === null) {
            $this->resourceMe = new Resource(
                ...
                $this->client
                    ->withOptions(
                        (new HttpOptions())
                            ->setAuthBearer($this->accessToken)
                            ->toArray()
                    )
                    ->request('GET', 'https://api.calendly.com/users/me')
                    ->toArray()['resource']
            );
        }
        return $this->resourceMe;
    }
}
