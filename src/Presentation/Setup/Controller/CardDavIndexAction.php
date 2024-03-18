<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Setup\Controller;

use App\Component\CardDav\Infrastructure\Doctrine\Repository\CardDavAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/setup/card_dav", name: "setup.card_dav.")]
#[IsGranted("ROLE_ADMIN")]
class CardDavIndexAction extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CardDavAccountRepository $repository): Response
    {
        return $this->render(
            '@setup/card_dav/index_embed.html.twig',
            [
                'card_dav_accounts' => $repository->findAll()
            ]
        );
    }
}
