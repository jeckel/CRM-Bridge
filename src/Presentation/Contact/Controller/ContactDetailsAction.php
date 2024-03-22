<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 22/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use App\Component\CardDav\Infrastructure\CardDav\CardDavClientProvider;
use App\Component\Contact\Infrastructure\Doctrine\Repository\ContactRepository;
use App\Component\Shared\Identity\ContactId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactDetailsAction extends AbstractController
{
    #[Route(
        path: "/contact/{contactId}/details",
        name: "contact.details",
        methods: ['GET']
    )]
    public function __invoke(
        string $contactId,
        ContactRepository $contactRepository,
        CardDavClientProvider $clientProvider
    ): Response {
        $id = ContactId::from($contactId);
        $contact = $contactRepository->getById($id);
        $vCard = $clientProvider->getClient($contact->cardDavAccount())
            ->getVCard($contact->vCardUri());
        return $this->render(
            '@contact/details.html.twig',
            [
                'contact' => $contact,
                'vCard' => $vCard
            ]
        );
    }
}
