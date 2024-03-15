<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/03/2024 09:01
 */
declare(strict_types=1);

namespace App\Presentation\Setup\Controller;

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
    ): Response {
        return $this->render(
            '@setup/index.html.twig',
        );
    }
}
