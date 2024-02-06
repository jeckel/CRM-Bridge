<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\CardDav;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use App\Presentation\Form\CardDav\DefaultAddressBookFormType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SelectAddressBookController extends AbstractController
{
    public function __construct(
        private readonly AddressBookDiscovery $addressBookDiscovery,
        private readonly ConfigurationService $configuration,
        private readonly AdminUrlGenerator $urlGenerator
    ) {}

    /**
     * @throws \Exception
     */
    #[Route(
        path: "/admin/carddav/select_address_book",
        name: "carddav_select_address_book",
        methods: ['GET', 'POST']
    )]
    public function selectAddressBook(
        Request $request,
    ): Response {
        $form = $this->createForm(
            type: DefaultAddressBookFormType::class,
            options: [
                'addressBooks' => $this->getList(),
                'data' => [
                    ConfigurationKey::CARDDAV_DEFAULT_ADDRESS_BOOK->value =>
                        $this->configuration->get(ConfigurationKey::CARDDAV_DEFAULT_ADDRESS_BOOK)
                ]
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, string> $formData */
            $formData = $form->getData();
            foreach ($formData as $key => $value) {
                $this->configuration->set(ConfigurationKey::from($key), $value);
            }
            return $this->redirect(
                $this->urlGenerator->setRoute(
                    'card_dav_list'
                )->generateUrl()
            );
        }

        return $this->render(
            'admin/page/form.html.twig',
            [
                'page_title' => 'card_dav.title.select_default_address_book',
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @return array<string, string>
     * @throws \Exception
     */
    private function getList(): array
    {
        $toReturn = [];
        foreach ($this->addressBookDiscovery->discoverAddressBooks() as $addressBook) {
            $toReturn[$addressBook->getName()] = $addressBook->getUri();
        }
        return $toReturn;
    }
}
