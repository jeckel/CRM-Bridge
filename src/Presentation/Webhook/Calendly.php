<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Webhook;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Calendly extends AbstractController
{
    #[Route(
        path: '/webhook/calendly',
        methods: ['GET', 'POST']
    )]
    public function calendly(): Response
    {
        return new Response('200 OK');
    }
}
