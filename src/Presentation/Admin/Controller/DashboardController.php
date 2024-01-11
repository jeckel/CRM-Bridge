<?php

namespace App\Presentation\Admin\Controller;

use App\Entity\IncomingWebhook;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CRM Bridge')
            ->setTranslationDomain('admin')
            ->renderContentMaximized()
            ->setLocales(['fr'])
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Incoming Webhooks', 'fas fa-list', IncomingWebhook::class);
        yield MenuItem::linkToRoute('Workers', 'fa fa-helmet-safety', 'worker_list');
        yield MenuItem::linkToRoute('Calendly Webhooks', 'fa fa-helmet-safety', 'calendly_webhook_list');
        yield MenuItem::linkToRoute('Linked-In', 'fa fa-helmet-safety', 'linkedin');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
