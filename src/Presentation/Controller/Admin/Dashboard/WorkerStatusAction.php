<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Dashboard;

use Supervisor\Supervisor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkerStatusAction extends AbstractController
{
    #[Route(
        path: '/admin/dashboard/parts/workers',
        name: 'dashboard_parts_worker_status',
        methods: ['GET']
    )]
    public function index(
        Supervisor $supervisor
    ): Response {
        return $this->render(
            'admin/dashboard/worker_status.html.twig',
            ['workers' => $supervisor->getAllProcesses()]
        );
    }
}
