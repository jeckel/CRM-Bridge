<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\CardDav;

use App\Infrastructure\CardDav\SyncManager;
use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use App\Presentation\Async\Message\SyncAddressBook;
use App\Presentation\Controller\Admin\ContactCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class SyncController extends AbstractController
{
    use ConfigurationCheckTrait;

    public function __construct(
        private readonly ConfigurationService $configuration,
        private readonly AdminUrlGenerator $urlGenerator,
        private readonly MessageBusInterface $messageBus
    ) {}

    /**
     * @throws \Exception
     */
    #[Route(
        path: "/admin/carddav/sync",
        name: "carddav_sync",
        methods: ['GET']
    )]
    public function syncAddressBook(): Response
    {
        if (($response = $this->checkRequiredConfiguration($this->configuration, $this->urlGenerator)) instanceof Response) {
            return $response;
        }

        $this->messageBus->dispatch(new SyncAddressBook());
        $this->addFlash(
            type: 'success',
            message: 'card_dav.alert.synchronization_success',
        );
        return $this->redirect(
            $this->urlGenerator->setAction(Action::INDEX)
                ->setController(ContactCrudController::class)
                ->generateUrl()
        );
    }
}
