<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Configuration;
use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ContactActivity;
use App\Infrastructure\Doctrine\Entity\ImapConfig;
use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use App\Infrastructure\Doctrine\Entity\Mail;
use App\Infrastructure\Doctrine\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\CrudMenuItem;
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
        /** @var User $user */
        $user = $this->getUser();
        return Dashboard::new()
            ->setTitle(sprintf('CRM Bridge<br /><small>%s</small>', $user->getAccountOrFail()))
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
