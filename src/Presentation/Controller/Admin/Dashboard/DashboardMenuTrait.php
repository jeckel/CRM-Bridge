<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Dashboard;

use App\Infrastructure\Doctrine\Entity\AccountService;
use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Configuration;
use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ImapConfig;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Infrastructure\Doctrine\Entity\Mail;
use App\Infrastructure\Doctrine\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

trait DashboardMenuTrait
{
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('menu.dashboard', 'fa fa-home');
        yield $this->filterByAccount(
            MenuItem::linkToCrud('menu.contacts', 'fas fa-id-card', Contact::class)
        );
        yield $this->filterByAccount(
            MenuItem::linkToCrud('menu.companies', 'fa fa-building', Company::class)
        );
        yield $this->filterByAccount(
            MenuItem::linkToCrud('menu.mail', 'fa fa-inbox', Mail::class)
        );
        yield MenuItem::subMenu('menu.config', 'fa fa-wrench')
            ->setSubItems([
                $this->filterByAccount(
                    MenuItem::linkToCrud('menu.card_dav', 'fas fa-id-card', CardDavConfig::class)
                        ->setPermission('ROLE_ADMIN')
                ),
                $this->filterByAccount(
                    MenuItem::linkToCrud('menu.imap', 'fa fa-inbox', ImapConfig::class)
                        ->setPermission('ROLE_ADMIN')
                ),
                $this->filterByAccount(
                    MenuItem::linkToCrud('menu.services', 'fas fa-concierge-bell', AccountService::class)
                        ->setPermission('ROLE_ADMIN')
                ),
                MenuItem::linkToRoute('menu.workers', 'fa fa-helmet-safety', 'worker_list')
                    ->setPermission('ROLE_SUPER_ADMIN'),
                MenuItem::linkToCrud('menu.incoming_webhooks', 'fas fa-sign-in-alt', IncomingWebhook::class)
                    ->setPermission('ROLE_SUPER_ADMIN'),
                MenuItem::linkToCrud('menu.setup_options', 'fas fa-wrench', Configuration::class)
                    ->setPermission('ROLE_SUPER_ADMIN'),
            ]);
    }

    protected function filterByAccount(CrudMenuItem $menuItem): CrudMenuItem
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            $menuItem->setQueryParameter('filters[account][comparison]', '=')
                ->setQueryParameter('filters[account][value]', $user->getAccountOrFail()->getId());
        }
        return $menuItem;
    }
}