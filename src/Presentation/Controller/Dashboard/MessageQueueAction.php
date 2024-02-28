<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/02/2024 08:49
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MessageQueueAction extends AbstractController
{
    #[Route(
        path: '/dashboard/parts/messengers',
        name: 'dashboard_parts_messengers',
        methods: ['GET']
    )]
    public function index(
    ): Response {
        return $this->render(
            'dashboard/messenger_status.html.twig',
            []
        );
    }
}
