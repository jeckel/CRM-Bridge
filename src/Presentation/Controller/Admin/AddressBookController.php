<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Infrastructure\ConfigurationService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddressBookController extends AbstractController
{
    public function __construct(
        private readonly AddressBookDiscovery $addressBookDiscovery,
        private readonly ConfigurationService $configuration,
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {}

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/admin/address_books',
        name: 'address_books_list',
        methods: ['GET']
    )]
    public function index(): Response
    {
        if (! $this->configuration->has('carddav.default_address_book')) {
            $this->addFlash('success', 'Aucun address book n\'a été défini pour');
            return $this->redirect(
                $this->adminUrlGenerator->setRoute(
                    'carddav_select_address_book'
                )->generateUrl()
            );
        }

        return $this->render(
            'admin/carddav/list_address_books.html.twig',
            ['address_books' => $this->addressBookDiscovery->discoverAddressBooks()]
        );
    }
}
