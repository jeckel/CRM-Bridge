<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/03/2024 20:08
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Dashboard;

use App\Infrastructure\Doctrine\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: "/dashboard/contacts",
    name: "dashboard.contacts.",
    methods: ['GET']
)]
class ContactSummaryController extends AbstractController
{
    #[Route(
        path: "/summary",
        name: "summary",
        methods: ['GET']
    )]
    public function __invoke(ContactRepository $repository): Response
    {
        return $this->render(
            'pages/dashboard/contacts_summary.html.twig',
            [
                'count' => $repository->count([]),
            ]
        );
    }

}
