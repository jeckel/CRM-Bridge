<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Infrastructure\Imap\ImapMailbox;
use App\Infrastructure\Imap\Mail\MailProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SandboxController extends AbstractController
{
    #[Route(
        path: "/sandbox/",
        name: "sandbox",
        methods: ['GET']
    )]
    public function index(
        //        ImapAccountRepository $repository,
        //        MailProvider $factory
    ): Response {
        //        $account = $repository->getById('9db73431-344f-46c9-8f44-754009921af1');
        //        $mailbox = ImapMailbox::fromImapAccount($account);
        //        dd($mailbox->listFolders());
        //        $imapMail = $mailbox->getMail(8168, "INBOX");
        // Message-Id: <20231208072537.2ea1c9490dc3ec37@mail.learndeskmail.com>

        //        $mail = $mailbox->searchMailbox('HEADER Message-ID <20231208072537.2ea1c9490dc3ec37@mail.learndeskmail.com>');
        //        $mail = $mailbox->searchMailbox('TEXT "<20230111T195622.5630805287003575535.expiry@letsencrypt.org>"');

        //        dd($mail);

        //        $mail = $factory->getMail($account, 'INBOX', 8168);

        //        dd($mail->subject(), $mail->fromName(), $mail); //, $imapMail, $imapMail->textPlain);


        return $this->render(
            'sandbox/index.html.twig',
            [
            ]
        );
    }
}
