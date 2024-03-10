<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/03/2024 11:42
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Setup;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Presentation\Form\Imap\ImapAccountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[Route("/setup/imap", name: "setup.imap.")]
#[IsGranted("ROLE_ADMIN")]
class AddImapAccountAction extends AbstractController
{
    #[Route(
        path: "/create_account",
        name: "create_account",
        methods: ['GET', 'POST']
    )]
    public function addAccount(Request $request, ImapAccountRepository $repository): Response
    {
        $form = $this->createForm(
            ImapAccountFormType::class,
            new ImapAccount(),
            [ 'action' => $this->generateUrl('setup.imap.create_account')]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ImapAccount $account */
            $account = $form->getData();
            $repository->persist($account);
            $this->addFlash('success', new TranslatableMessage(
                'setup.flash_message.imap_account_added',
                ['%account%' => $account->getName()],
                'admin'
            ));
            return $this->redirectToRoute('setup.index');
        }
        return $this->render(
            'setup/imap_account_form.html.twig',
            [
                'page_title' => 'mail.title.create_contact',
                'form' => $form->createView()
            ]
        );
    }
}
