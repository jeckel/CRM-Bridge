<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LinkedInController extends AbstractController
{
    #[Route(
        path: '/admin/linkedin',
        name: 'linkedin',
        methods: ['GET']
    )]
    public function index(): Response
    {
        dd('foobar');
    }

    #[Route(
        path: '/linkedin/callback',
        methods: ['GET', 'POST']
    )]
    public function callback(Request $request): Response
    {
        dd($request->get('error'), $request->toArray());
    }
}
