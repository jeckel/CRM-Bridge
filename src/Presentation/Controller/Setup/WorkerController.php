<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Setup;

use Supervisor\Supervisor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route(
    path: '/setup/workers',
    name: 'setup.worker.',
)]
class WorkerController extends AbstractController
{
    public function __construct(
        private readonly Supervisor $supervisor
    ) {}


    #[Route(
        path: '/',
        name: 'index',
    )]
    public function index(): Response
    {
        return $this->render(
            'pages/setup/worker/index.html.twig',
            ['workers' => $this->supervisor->getAllProcesses()]
        );
    }

    #[Route(
        path: '/{group}/{name}',
        name: 'details',
        methods: ['GET']
    )]
    public function details(string $name, string $group): Response
    {
        $processName = "$group:$name";
        return $this->render(
            'pages/setup/worker/details.html.twig',
            [
                'worker' => $this->supervisor->getProcess($processName),
                'stdoutLog' => $this->supervisor->tailProcessStdoutLog($processName, 0, 10000),
                'stderrLog' => $this->supervisor->tailProcessStderrLog($processName, 0, 10000),
            ]
        );
    }

    #[Route(
        path: '/{group}/{name}/start',
        name: 'start',
        methods: ['GET']
    )]
    public function start(string $name, string $group, Request $request): Response
    {
        $this->supervisor->startProcess("$group:$name");
        $this->addFlash('success', new TranslatableMessage(
            'worker.flash_message.worker_started',
            ['%worker%' => "$group:$name"]
        ));
        return $this->redirectToReferer($request);
    }

    #[Route(
        path: '/{group}/{name}/stop',
        name: 'stop',
        methods: ['GET']
    )]
    public function stop(string $name, string $group, Request $request): Response
    {
        $this->supervisor->stopProcess("$group:$name");
        $this->addFlash('success', new TranslatableMessage(
            'worker.flash_message.worker_stopped',
            ['%worker%' => "$group:$name"]
        ));
        return $this->redirectToReferer($request);
    }

    #[Route(
        path: '/{group}/{name}/restart',
        name: 'restart',
        methods: ['GET']
    )]
    public function restart(string $name, string $group, Request $request): Response
    {
        $this->supervisor->stopProcess("$group:$name");
        $this->supervisor->startProcess("$group:$name");
        $this->addFlash('success', new TranslatableMessage(
            'worker.flash_message.worker_restarted',
            ['%worker%' => "$group:$name"]
        ));
        return $this->redirectToReferer($request);
    }

    #[Route(
        path: '/{group}/{name}/clear_logs',
        name: 'clear_logs',
        methods: ['GET']
    )]
    public function clearLogs(string $name, string $group): Response
    {
        $this->supervisor->clearProcessLogs("$group:$name");
        return $this->redirectToDetails($name, $group);
    }

    private function redirectToReferer(Request $request): Response
    {
        /** @var string $url */
        $url = $request->server->get('HTTP_REFERER');
        /** @var string $path */
        $path = parse_url($url, PHP_URL_PATH);
        return $this->redirect($path);
    }

    private function redirectToDetails(string $name, string $group): Response
    {
        return $this->redirectToRoute(
            'setup.worker.details',
            ['name' => $name, 'group' => $group]
        );
    }
}
