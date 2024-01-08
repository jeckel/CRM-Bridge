<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Admin\Controller;

use App\Infrastructure\Calendly\CalendlyClient;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CalendlyWebhookController extends AbstractController
{
    public function __construct(
        private readonly CalendlyClient $calendly,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {}

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

    #[Route(
        path: '/admin/calendly/webhooks/register',
        name: 'calendly_register_webhook',
        methods: ['GET']
    )]
    public function new(): Response
    {
        $this->calendly->createWebhook();

        return $this->redirect(
            $this->adminUrlGenerator->setRoute(
                'calendly_webhook_list'
            )->generateUrl()
        );
    }

    #[Route(
        path: '/admin/calendly/webhooks/{uid}/unregister',
        name: 'calendly_unregister_webhook',
        methods: ['GET']
    )]
    public function delete(string $uid): Response
    {
        $this->calendly->deleteWebhook($uid);

        return $this->redirect(
            $this->adminUrlGenerator->setRoute(
                'calendly_webhook_list'
            )->generateUrl()
        );
    }
}
