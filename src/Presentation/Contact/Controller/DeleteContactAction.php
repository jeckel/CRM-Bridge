<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function delete(string $contactId): Response
    {
        // @todo: Delete on CardDav Server
        dd($contactId);
//        $contact = $this->contactRepository->getById($contactId);
//        $this->contactRepository->remove($contact);
//        return $this->redirectToRoute('contact.index');
    }
}
