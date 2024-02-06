<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Imap;

use App\Infrastructure\Imap\MailboxSynchronizer;
use App\Presentation\Async\Message\SyncMailbox;
use App\Presentation\Controller\Admin\MailCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class SyncMailboxController extends AbstractController
{
    public function __construct(
        //        private readonly MailboxSynchronizer $mailboxSynchronizer,
        private readonly AdminUrlGenerator $urlGenerator,
        private readonly MessageBusInterface $messageBus
    ) {}

    #[Route(
        path: '/admin/imap/check',
        name: 'imap_sync',
        methods: ['GET']
    )]
    public function __invoke(): Response
    {
        $this->messageBus->dispatch(new SyncMailbox());
        //        $this->mailboxSynchronizer->sync();
        $this->addFlash(
            type: 'success',
            message: 'imap.alert.synchronization_request_success',
        );
        return $this->redirect(
            $this->urlGenerator->setAction(Action::INDEX)
                ->setController(MailCrudController::class)
                ->generateUrl()
        );
    }
}
