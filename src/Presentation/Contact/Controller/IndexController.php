<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/03/2024 13:00
 */
declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Infrastructure\CardDav\CardDavClientProvider;
use App\Infrastructure\CardDav\VCardHelper;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 25;
        $contacts = $paginator->paginate(
            $this->contactRepository->createQueryList(),
            $page,
            $limit,
            [
                'defaultSortFieldName' => ['c.displayName'],
                'defaultSortDirection' => 'asc',
            ]
        );
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
        $contact = $this->contactRepository->getById($contactId);
        return $this->render('@contact/details.html.twig', ['contact' => $contact]);
    }

    #[Route(
        path: '/{contactId}/delete',
        name: 'delete',
        methods: ['GET']
    )]
    public function delete(string $contactId): Response
    {
        $contact = $this->contactRepository->getById($contactId);
        $this->contactRepository->remove($contact);
        return $this->redirectToRoute('contact.index');
    }

    #[Route(path: '/{contactId}/dumpCardDav', name: 'contact.debug', methods: ['GET'])]
    public function dumpCardDav(string $contactId, CardDavClientProvider $cardDavClientProvider): Response
    {
        $contact = $this->contactRepository->getById($contactId);
        $vCardUri = $contact->getVCardUri();
        if (null === $vCardUri) {
            dd(null);
        }
        $vCard = $cardDavClientProvider
            ->getClient($contact->getCardDavAccountOrFail())
            ->getVCard($vCardUri);
        //        dd($vCard->uid());
        dd($vCard);
    }
}
