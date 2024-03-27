<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/03/2024 11:42
 */
declare(strict_types=1);

namespace App\Presentation\Setup\Controller;

use App\Component\WebMail\Application\Command\CreateImapAccount;
use App\Presentation\Setup\Form\ImapAccountFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted("ROLE_ADMIN")]
class ImapAddAccountAction extends AbstractController
{
    #[Route(
        path: "/setup/imap/create_account",
        name: "setup.imap.create_account",
        methods: ['GET', 'POST']
    )]
    public function __invoke(Request $request, MessageBusInterface $messageBus): Response
    {
        $form = $this->createForm(
            ImapAccountFormType::class,
            null,
            [ 'hx-post' => $this->generateUrl('setup.imap.create_account')]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateImapAccount $command */
            $command = $form->getData();
            $messageBus->dispatch($command);

            return $this->render('@modal/success.html.twig', [
                'message' => new TranslatableMessage(
                    'setup.flash_message.imap_account_added',
                    ['%command%' => $command->name],
                    'messages'
                ),
                'refresh' => [
                    'route' => 'setup.imap.index',
                    'target' => '#imapList'
                ]
            ]);
        }

        return $this->render(
            '@modal/form.html.twig',
            [
                'title' => 'setup.imap.title.create_account',
                'form' => $form->createView()
            ]
        );
    }
}
