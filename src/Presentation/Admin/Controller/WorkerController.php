<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Supervisor\Supervisor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkerController extends AbstractController
{
    public function __construct(
        private readonly Supervisor $supervisor,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {}

    #[Route(
        path: '/admin/workers',
        name: 'worker_list',
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render(
            'admin/worker/list.html.twig',
            ['workers' => $this->supervisor->getAllProcesses()]
        );
    }

    #[Route(
        path: '/admin/worker/{group}/{name}',
        name: 'worker_details',
        methods: ['GET']
    )]
    public function details(string $name, string $group): Response
    {
        $processName = "$group:$name";
        return $this->render(
            'admin/worker/process.html.twig',
            [
                'worker' => $this->supervisor->getProcess($processName),
                'stdoutLog' => $this->supervisor->tailProcessStdoutLog($processName, 0, 10000),
                'stderrLog' => $this->supervisor->tailProcessStderrLog($processName, 0, 10000),
            ]
        );
    }

    #[Route(
        path: '/admin/worker/{group}/{name}/start',
        name: 'worker_start',
        methods: ['GET']
    )]
    public function start(string $name, string $group): Response
    {
        $this->supervisor->startProcess("$group:$name");
        return $this->redirectToDetails($name, $group);
    }

    #[Route(
        path: '/admin/worker/{group}/{name}/stop',
        name: 'worker_stop',
        methods: ['GET']
    )]
    public function __invoke(string $name, string $group): Response
    {
        $this->supervisor->stopProcess("$group:$name");
        return $this->redirectToDetails($name, $group);
    }

    #[Route(
        path: '/admin/worker/{group}/{name}/clear_logs',
        name: 'worker_clear_logs',
        methods: ['GET']
    )]
    public function clearLogs(string $name, string $group): Response
    {
        $this->supervisor->clearProcessLogs("$group:$name");
        return $this->redirectToDetails($name, $group);
    }

    private function redirectToDetails(string $name, string $group): Response
    {
        return $this->redirect(
            $this->adminUrlGenerator->setRoute(
                'worker_details',
                ['name' => $name, 'group' => $group]
            )->generateUrl()
        );
    }
}
