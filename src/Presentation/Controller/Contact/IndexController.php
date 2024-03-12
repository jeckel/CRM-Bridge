<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/03/2024 13:00
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Contact;

use App\Infrastructure\Doctrine\Entity\CardDavAddressBook;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\ContactEmailAddress;
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
    public function index(): Response
    {
        return $this->render('pages/contact/index.html.twig');
    }

    #[Route(
        path: "/contacts",
        name: "contacts",
        methods: ['GET']
    )]
    public function list(
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 25;
        $contacts = $paginator->paginate(
            $this->contactRepository->createQueryBuilder('c')
                ->select(
                    'c.id',
                    'c.displayName',
                    'c.phoneNumber',
                    'co.name as companyName',
                    'e.emailAddress',
                    'a.name as addressBook'
                )
                ->innerJoin(CardDavAddressBook::class, 'a', 'WITH', 'c.addressBook = a.id')
                ->leftJoin(Company::class, 'co', 'WITH', 'co.id = c.company')
                ->leftJoin(ContactEmailAddress::class, 'e', 'WITH', 'c.id = e.contact'),
            $page,
            $limit,
            [
                'defaultSortFieldName' => ['c.displayName'],
                'defaultSortDirection' => 'asc',
            ]
        );
        return $this->render('pages/contact/list_embed.html.twig', [
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
        return $this->render('pages/contact/details.html.twig', ['contact' => $contact]);
    }
}
