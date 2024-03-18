<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use App\Component\CardDav\Application\Command\DeleteCardDavContact;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route(
    path: "/contact",
    name: "contact."
)]
class DeleteContactAction extends AbstractController
{
    #[Route(
        path: '/{contactId}/delete',
        name: 'delete',
        methods: ['GET']
    )]
    public function delete(
        string $contactId,
        MessageBusInterface $messageBus,
        ContactRepository $repository
    ): Response {
        $contact = $repository->getById($contactId);
        $messageBus->dispatch(new DeleteCardDavContact($contact->getIdentity()));
        $this->addFlash('success', new TranslatableMessage(
            'contact.flash_message.contact_deleted',
            ['%contact%' => $contact->getDisplayName()]
        ));
        return $this->redirectToRoute('contact.index');
    }
}
