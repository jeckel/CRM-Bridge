<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 14/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Dashboard\Controller;

use App\Infrastructure\Doctrine\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: "/dashboard/companies",
    name: "dashboard.companies.",
    methods: ['GET']
)]
class CompanySummaryController extends AbstractController
{
    #[Route(
        path: "/summary",
        name: "summary",
        methods: ['GET']
    )]
    public function __invoke(CompanyRepository $repository): Response
    {
        return $this->render(
            '@dashboard/companies_summary.html.twig',
            [
                'count' => $repository->count([]),
            ]
        );
    }
}
