<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\CardDav;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Infrastructure\Configuration\ConfigurationService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CardDavDashboardController extends AbstractController
{
    use ConfigurationCheckTrait;

    public function __construct(
        //        private readonly AddressBookDiscovery $addressBookDiscovery,
        private readonly ConfigurationService $configuration,
        private readonly AdminUrlGenerator $urlGenerator
    ) {}

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/admin/carddav',
        name: 'card_dav_list',
        methods: ['GET']
    )]
    public function index(): Response
    {
        if (($response = $this->checkRequiredConfiguration($this->configuration, $this->urlGenerator)) instanceof Response) {
            return $response;
        }

        return $this->render(
            'admin/card_dav/list_address_books.html.twig',
            ['address_books' => []]
            //            ['address_books' => $this->addressBookDiscovery->discoverAddressBooks()]
        );
    }
}
