<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Configuration;
use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ContactActivity;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Infrastructure\Doctrine\Entity\Mail;
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
            ->setDateFormat('dd/MM/yyyy')
            ->setDateTimeFormat('dd/MM/yyyy HH:mm')
            ->setTimeFormat('HH:mm')
            ->setPaginatorPageSize(100)
            ->showEntityActionsInlined();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('menu.dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('menu.contacts', 'fas fa-id-card', Contact::class);
        yield MenuItem::linkToCrud('menu.companies', 'fa fa-building', Company::class);
        yield MenuItem::linkToCrud('menu.mail', 'fa fa-inbox', Mail::class);
        //        yield MenuItem::linkToCrud('Contact Activities', 'fas fa-list', ContactActivity::class);
        //        yield MenuItem::linkToRoute('EspoCRM Contacts', 'fa fa-helmet-safety', 'espo_crm_contacts');
        yield MenuItem::subMenu('menu.config', 'fa fa-wrench')
            ->setSubItems([
                MenuItem::linkToCrud('menu.card_dav', 'fas fa-id-card', CardDavConfig::class),
                MenuItem::linkToRoute('menu.card_dav', 'fa fa-sync', 'card_dav_list'),
                MenuItem::linkToRoute('menu.imap', 'fa fa-inbox', 'imap_setup'),
                MenuItem::linkToRoute('menu.workers', 'fa fa-helmet-safety', 'worker_list'),
                MenuItem::linkToCrud('menu.incoming_webhooks', 'fas fa-sign-in-alt', IncomingWebhook::class),
                MenuItem::linkToCrud('menu.setup_options', 'fas fa-wrench', Configuration::class)
            ]);
    }
}
