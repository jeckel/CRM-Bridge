<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Setup\Controller;

use App\Component\CardDav\Application\Command\CreateCardDavAccount;
use App\Presentation\Setup\Form\CardDavAccountFormType;
use App\Presentation\Setup\Query\CardDavAccountIdFromUri;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/setup/card_dav", name: "setup.card_dav.")]
#[IsGranted("ROLE_ADMIN")]
class CardDavAddAccountAction extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route(
        path: "/create_account",
        name: "create_account",
        methods: ['GET', 'POST']
    )]
    public function addAccount(
        Request $request,
        CardDavAccountIdFromUri $accountIdFromUri,
        MessageBusInterface $messageBus
    ): Response {
        $form = $this->createForm(
            CardDavAccountFormType::class,
            null,
            [
                'hx-post' => $this->generateUrl('setup.card_dav.create_account'),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateCardDavAccount $command */
            $command = $form->getData();
            $messageBus->dispatch($command);
            return $this->redirect($this->generateUrl(
                'setup.card_dav.setup_address_books',
                ['accountId' => (string) $accountIdFromUri($command->uri)]
            ));
        }
        return $this->render(
            '@modal/form.html.twig',
            [
                'title' => 'setup.card_dav.title.create_account',
                'form' => $form->createView()
            ]
        );
    }
}
