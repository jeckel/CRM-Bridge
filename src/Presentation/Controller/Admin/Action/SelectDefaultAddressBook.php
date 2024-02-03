<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Action;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Presentation\Form\DefaultAddressBookFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class SelectDefaultAddressBook extends AbstractController
{
    #[Route(
        path: "/admin/carddav/select_address_book",
        name: "carddav_select_address_book",
        methods: ['GET', 'POST']
    )]
    public function __invoke(
        AddressBookDiscovery $addressBookDiscovery,
        Request $request,
    ): Response {
        $form = $this->createForm(
            type: DefaultAddressBookFormType::class,
            options: ['addressBooks' => $this->getList($addressBookDiscovery)]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render(
            'admin/carddav/select_address_book.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @param AddressBookDiscovery $addressBookDiscovery
     * @return array<string, string>
     * @throws \Exception
     */
    private function getList(AddressBookDiscovery $addressBookDiscovery): array
    {
        $toReturn = [];
        foreach ($addressBookDiscovery->discoverAddressBooks() as $addressBook) {
            $toReturn[$addressBook->getName()] = $addressBook->getName();
        }
        return $toReturn;
    }
}
