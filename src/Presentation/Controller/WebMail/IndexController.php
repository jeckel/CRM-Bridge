<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Presentation\Controller\Admin\ImapConfigCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route(
        path: "/webmail/",
        name: "webmail_index",
        methods: ['GET']
    )]
    public function index(ImapAccountRepository $imapAccountRepo, AdminUrlGenerator $urlGenerator): Response
    {
        if ($imapAccountRepo->count() === 0) {
            return $this->redirect(
                $urlGenerator->setAction(Action::NEW)
                    ->setController(ImapConfigCrudController::class)
                    ->generateUrl()
            );
        }
        return $this->render(
            'webmail/index.html.twig',
        );
    }
}
