<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 19:14
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Action;

use App\Infrastructure\Doctrine\Repository\IncomingWebhookRepository;
use App\Presentation\Controller\Admin\IncomingWebhookCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class ReplayWebhookAction extends AbstractController
{
    #[Route(
        path: "/admin/webhook/{webhookId}/replay",
        name: "webhook_replay",
        methods: ['GET']
    )]
    public function __invoke(
        string $webhookId,
        MessageBusInterface $messageBus,
        IncomingWebhookRepository $repository,
        AdminUrlGenerator $urlGenerator
    ): Response {
        $messageBus->dispatch($repository->getById($webhookId));
        $this->addFlash('success', 'Webhook re-added to the queue.');
        return $this->redirect(
            $urlGenerator->setAction(Action::DETAIL)
                ->setEntityId($webhookId)
                ->setController(IncomingWebhookCrudController::class)
                ->generateUrl()
        );
    }
}
