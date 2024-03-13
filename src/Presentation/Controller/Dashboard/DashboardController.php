<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 29/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route(
        path: "/",
        name: "home",
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render(
            'pages/dashboard/index.html.twig',
        );
    }
}
