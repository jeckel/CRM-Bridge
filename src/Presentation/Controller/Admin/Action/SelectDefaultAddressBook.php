<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Action;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Infrastructure\Doctrine\Entity\Configuration;
use App\Presentation\Form\DefaultAddressBookFormType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class SelectDefaultAddressBook extends AbstractController
{
    public function __construct(
        private readonly AddressBookDiscovery $addressBookDiscovery,
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $urlGenerator
    ) {}

    /**
     * @throws Exception
     */
    #[Route(
        path: "/admin/carddav/select_address_book",
        name: "carddav_select_address_book",
        methods: ['GET', 'POST']
    )]
    public function __invoke(
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
                    'address_books_list'
                )->generateUrl()
            );
        }

        return $this->render(
            'admin/carddav/select_address_book.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @return array<string, string>
     * @throws Exception
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
