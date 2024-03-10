<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/03/2024 11:54
 */
declare(strict_types=1);

namespace App\Presentation\Controller\Setup;

use App\Infrastructure\Doctrine\Entity\CardDavAccount;
use App\Infrastructure\Doctrine\Repository\CardDavConfigRepository;
use App\Presentation\Form\CardDav\CardDavAccountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[Route("/setup/card_dav", name: "setup.card_dav.")]
#[IsGranted("ROLE_ADMIN")]
class AddCardDavAccountAction extends AbstractController
{
    #[Route(
        path: "/create_account",
        name: "create_account",
        methods: ['GET', 'POST']
    )]
    public function addAccount(Request $request, CardDavConfigRepository $repository): Response
    {
        $form = $this->createForm(
            CardDavAccountFormType::class,
            new CardDavAccount(),
            [ 'action' => $this->generateUrl('setup.card_dav.create_account')]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CardDavAccount $account */
            $account = $form->getData();
            $repository->persist($account);
            $this->addFlash('success', new TranslatableMessage(
                'setup.flash_message.card_dav_account_added',
                ['%account%' => $account->getName()],
                'admin'
            ));
            return $this->redirectToRoute('setup.index');
        }
        return $this->render(
            'setup/card_dav_account_form.html.twig',
//            'webmail/imap_account_form.html.twig',
            [
                'page_title' => 'mail.title.create_contact',
                'form' => $form->createView()
            ]
        );
    }
}
