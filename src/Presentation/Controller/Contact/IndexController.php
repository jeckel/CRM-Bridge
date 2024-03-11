<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/03/2024 13:00
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Contact;

use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Contact;
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
    #[Route(
        path: "/",
        name: "index",
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig');
    }

    #[Route(
        path: "/contacts",
        name: "contacts",
        methods: ['GET']
    )]
    public function list(
        ContactRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 20;
        $contacts = $paginator->paginate(
            $repository->createQueryBuilder('c')
                ->select('c.id', 'c.displayName', 'c.phoneNumber', 'co.name as companyName', 'e.emailAddress')
                ->leftJoin(Company::class, 'co', 'WITH', 'co.id = c.company')
                ->leftJoin(ContactEmailAddress::class, 'e', 'WITH', 'c.id = e.contact'),
            $page,
            $limit
        );
        return $this->render('contact/embed/list.html.twig', [
            'contacts' => $contacts,
            'page' => $page,
            'limit' => $limit,
            'total' => $contacts->getTotalItemCount()
        ]);
    }
}
