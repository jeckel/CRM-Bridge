<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 01/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Presentation\Form\Imap\ImapAccountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function App\new_uuid;

class ImapAccountController extends AbstractController
{
    public function __construct(
        private readonly ImapAccountRepository $repository
    ) {}

    #[Route(
        path: "/webmail/imap",
        name: "webmail_imap_list",
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render(
            'webmail/imap_account_list.html.twig',
            [
                'accounts' => $this->repository->findAll(),
            ]
        );
    }

    #[Route(
        path: "/webmail/imap/create_account",
        name: "webmail_imap_create_account",
        methods: ['GET', 'POST']
    )]
    public function addAccount(Request $request): Response
    {
        $form = $this->createForm(
            ImapAccountFormType::class,
            new ImapAccount()
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ImapAccount $account */
            $account = $form->getData();
            $account->setId(new_uuid());
            $this->repository->persist($account);
            return $this->redirectToRoute('webmail_imap_list');
        }
        return $this->render(
            'webmail/imap_account_form.html.twig',
            [
                'page_title' => 'mail.title.create_contact',
                'form' => $form->createView()
            ]
        );
    }
}
