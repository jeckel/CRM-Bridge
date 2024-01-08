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
        private readonly string $webhookUrl,
        private readonly HttpClientInterface $client
    ) {}

    /**
     * @return iterable<WebhookConfig>
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
            yield new WebhookConfig(...$webhook);
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

    public function createWebhook(): WebhookConfig
    {
        $response = $this->client
            ->withOptions(
                (new HttpOptions())
                    ->setAuthBearer($this->accessToken)
                    ->toArray()
            )
            ->request(
                method: 'POST',
                url: 'https://api.calendly.com/webhook_subscriptions',
                options: [
                    'json' => [
                        'url' => $this->webhookUrl,
                        'events' => [
                            'invitee.created',
                            'invitee.canceled',
                            'invitee_no_show.created'
                        ],
                        'organization' => $this->resourceMe()->current_organization,
                        'user' => $this->resourceMe()->uri,
                        'scope' => 'organization'
                    ]
                ]
            );
        return new WebhookConfig(...$response->toArray()['resource']);
    }

    public function deleteWebhook(string $uid): void
    {
        $this->client
            ->withOptions(
                (new HttpOptions())
                    ->setAuthBearer($this->accessToken)
                    ->toArray()
            )
            ->request(
                method: 'DELETE',
                url: 'https://api.calendly.com/webhook_subscriptions/' . $uid
            );
    }
}
