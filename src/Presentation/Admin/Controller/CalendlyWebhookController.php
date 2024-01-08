<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Admin\Controller;

use App\Infrastructure\Calendly\CalendlyClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendlyWebhookController extends AbstractController
{
    public function __construct(
        private readonly CalendlyClient $calendly
    ) {
    }

    #[Route(
        path: '/admin/calendly/webhooks',
        name: 'calendly_webhook_list',
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render(
            'admin/calendly/webhook/list.html.twig',
            ['webhooks' => $this->calendly->listWebhooks()]
        );
    }
}
