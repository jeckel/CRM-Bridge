<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use App\Infrastructure\Doctrine\Entity\Configuration;
use App\Presentation\Form\CardDav\DefaultAddressBookFormType;
use App\Presentation\Form\CardDav\SetupConnectionFormType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/admin/card_dav'
)]
class CardDavController extends AbstractController
{
    public function __construct(
        private readonly AddressBookDiscovery $addressBookDiscovery,
        private readonly ConfigurationService $configuration,
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $urlGenerator
    ) {}

    /**
     * @throws \Exception
     */
    #[Route(
        path: '',
        name: 'card_dav_list',
        methods: ['GET']
    )]
    public function index(): Response
    {
        if (($response = $this->checkRequiredConfiguration()) instanceof Response) {
            return $response;
        }

        return $this->render(
            'admin/card_dav/list_address_books.html.twig',
            ['address_books' => $this->addressBookDiscovery->discoverAddressBooks()]
        );
    }

    #[Route(
        path: '/setup',
        name: 'card_dav_setup',
        methods: ['GET', 'POST']
    )]
    public function setupConfiguration(Request $request): Response
    {
        $form = $this->createForm(type: SetupConnectionFormType::class);
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
                'page_title' => 'card_dav.title.setup_connection',
                'form' => $form->createView()
            ]
        );
    }

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
            options: ['addressBooks' => $this->getList()]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Configuration $entity */
            $entity = $form->getData();
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
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

    private function checkRequiredConfiguration(): ?Response
    {
        if (! $this->configuration->has(
            ConfigurationKey::CARDDAV_URI,
            ConfigurationKey::CARDDAV_USERNAME,
            ConfigurationKey::CARDDAV_PASSWORD
        )) {
            $this->addFlash(
                type: 'warning',
                message: 'card_dav.alert.missing_required_configuration'
            );
            return $this->redirect(
                $this->urlGenerator->setRoute(
                    'card_dav_setup'
                )->generateUrl()
            );
        }

        if (! $this->configuration->has(ConfigurationKey::CARDDAV_DEFAULT_ADDRESS_BOOK)) {
            $this->addFlash(
                type: 'warning',
                message: 'card_dav.alert.default_address_book_not_set'
            );
            return $this->redirect(
                $this->urlGenerator->setRoute(
                    'carddav_select_address_book'
                )->generateUrl()
            );
        }
        return null;
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
