<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/03/2024 13:00
 */
declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use App\Component\CardDav\Infrastructure\CardDav\CardDavClientProvider;
use App\Component\Contact\Infrastructure\Doctrine\Repository\ContactRepository;
use App\Component\Shared\Identity\ContactId;
use App\Presentation\Contact\Query\ContactListQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: "/contact",
    name: "contact."
)]
class IndexController extends AbstractController
{
    public function __construct(private readonly ContactRepository $contactRepository) {}

    #[Route(
        path: "/",
        name: "index",
        methods: ['GET']
    )]
    public function index(
        Request $request,
        ContactListQuery $contactListQuery
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 25;
        $contacts = $contactListQuery($page, $limit);
        return $this->render('@contact/index.html.twig', [
            'contacts' => $contacts,
            'page' => $page,
            'limit' => $limit,
            'total' => $contacts->getTotalItemCount()
        ]);
    }

    #[Route(
        path: "/{contactId}/details",
        name: "contact.details",
        methods: ['GET']
    )]
    public function details(string $contactId): Response
    {
        $id = ContactId::from($contactId);
        $contact = $this->contactRepository->getById($id);
        return $this->render('@contact/details.html.twig', ['contact' => $contact]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route(path: '/{contactId}/dumpCardDav', name: 'contact.debug', methods: ['GET'])]
    public function dumpCardDav(#[MapQueryParameter] ContactId $contactId, CardDavClientProvider $cardDavClientProvider): Response
    {
        $contact = $this->contactRepository->getById($contactId);
        dd($contact);
//        $vCardUri = $contact->getVCardUri();
//        if (null === $vCardUri) {
//            dd(null);
//        }
//        $vCard = $cardDavClientProvider
//            ->getClient($contact->getCardDavAccountOrFail())
//            ->getVCard($vCardUri);
//        //        dd($vCard->uid());
//        dd($vCard);
    }
}
