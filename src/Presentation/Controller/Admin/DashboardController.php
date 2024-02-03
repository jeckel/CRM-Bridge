<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ContactActivity;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateTimeFormat('dd/MM/yyyy HH:mm:ss')
            ->setPaginatorPageSize(100)
            ->showEntityActionsInlined();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Incoming Webhooks', 'fas fa-list', IncomingWebhook::class);
        yield MenuItem::linkToCrud('Contacts', 'fas fa-list', Contact::class);
        yield MenuItem::linkToCrud('Contact Activities', 'fas fa-list', ContactActivity::class);
        yield MenuItem::linkToRoute('Workers', 'fa fa-helmet-safety', 'worker_list');
        yield MenuItem::linkToRoute('EspoCRM Contacts', 'fa fa-helmet-safety', 'espo_crm_contacts');
        yield MenuItem::linkToRoute('Calendly Webhooks', 'fa fa-helmet-safety', 'calendly_webhook_list');
        yield MenuItem::linkToRoute('Linked-In', 'fa fa-helmet-safety', 'linkedin');
        yield MenuItem::linkToRoute('Address Book', 'fa fa-helmet-safety', 'address_books_list');
    }
}
