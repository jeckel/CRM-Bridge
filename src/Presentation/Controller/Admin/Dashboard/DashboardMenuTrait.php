<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Dashboard;

use App\Infrastructure\Doctrine\Entity\ServiceConnector;
use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Configuration;
use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ImapConfig;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Infrastructure\Doctrine\Entity\Mail;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

trait DashboardMenuTrait
{
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('menu.dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('menu.contacts', 'fas fa-id-card', Contact::class);
        yield MenuItem::linkToCrud('menu.companies', 'fa fa-building', Company::class);
        yield MenuItem::linkToCrud('menu.mail', 'fa fa-inbox', Mail::class);
        yield MenuItem::linkToRoute('menu.webmail', 'fa fa-inbox', 'webmail_index');
        yield MenuItem::section('menu.admin');
        yield MenuItem::subMenu('menu.config', 'fa fa-wrench')
            ->setSubItems([
                MenuItem::linkToCrud('menu.card_dav', 'fas fa-id-card', CardDavConfig::class),
                MenuItem::linkToCrud('menu.imap', 'fa fa-inbox', ImapConfig::class),
                MenuItem::linkToCrud('menu.services', 'fas fa-concierge-bell', ServiceConnector::class),
            ])
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::subMenu('menu.super_admin', 'fa fa-wrench')
            ->setSubItems([
                MenuItem::linkToRoute('menu.workers', 'fa fa-helmet-safety', 'worker_list'),
                MenuItem::linkToCrud('menu.incoming_webhooks', 'fas fa-sign-in-alt', IncomingWebhook::class),
                MenuItem::linkToCrud('menu.setup_options', 'fas fa-wrench', Configuration::class),
            ])
            ->setPermission('ROLE_SUPER_ADMIN');
    }
}
