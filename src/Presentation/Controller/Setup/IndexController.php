<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/03/2024 09:01
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Setup;

use App\Infrastructure\Doctrine\Repository\CardDavConfigRepository;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/setup", name: "setup.")]
#[IsGranted("ROLE_ADMIN")]
class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        ImapAccountRepository $imapAccountRepository,
        CardDavConfigRepository $cardDavConfigRepository
    ): Response {
        return $this->render(
            'setup/index.html.twig',
            [
                'imap_accounts' => $imapAccountRepository->findAll(),
                'card_dav_accounts' => $cardDavConfigRepository->findAll()
            ]
        );
    }
}
