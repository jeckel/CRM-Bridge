<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Webhook;

use App\Component\Shared\ValueObject\WebHookSource;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Infrastructure\Doctrine\Repository\IncomingWebhookRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractWebhookController extends AbstractController
{
    public function __construct(
        protected readonly IncomingWebhookRepository $incomingWebhookRepository
    ) {}

    /**
     * @param array<string, mixed> $content
     */
    protected function persistWebhook(
        WebHookSource $source,
        DateTimeImmutable $createdAt,
        string|\Stringable $event,
        array $content
    ): IncomingWebhook {
        $webhook = (new IncomingWebhook())
            ->setSource($source->value)
            ->setCreatedAt($createdAt)
            ->setEvent((string) $event)
            ->setPayload($content);
        $this->incomingWebhookRepository->persist(
            $webhook
        );
        return $webhook;
    }
}
