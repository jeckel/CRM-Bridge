<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use App\Infrastructure\Imap\Mail\MailProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use ZBateson\MailMimeParser\Message;

class MailDetailController extends AbstractController
{
    #[Route(
        path: '/webmail/{mailId}',
        name: 'webmail_mail',
        methods: ['GET', 'PUT']
    )]
    public function __invoke(string $mailId, ImapMessageRepository $repository): Response
    {
        $mail = $repository->getById($mailId);
        return $this->render('webmail/mail.html.twig', ['mail' => $mail]);
    }

    #[Route(
        path: '/webmail/{mailId}/debug',
        name: 'webmail_mail_debug',
        methods: ['GET', 'PUT']
    )]
    public function debug(string $mailId, ImapMessageRepository $repository, MailProvider $provider): Response
    {
        $entityMail = $repository->getById($mailId);
        $fullMail = $provider->getMail($entityMail->getImapAccountOrFail(), $entityMail->getFolder(), $entityMail->getUid());
        $fullMail->getImapMail();
        $parsedHeaders = Message::from($fullMail->getEntity()->getHeaderRaw(), true);

        return $this->render(
            'debug/dump_var.html.twig',
            ['var' => [
                'original' => $fullMail,
                'parsed' => $parsedHeaders,
                'spam' => $parsedHeaders->getHeader('X-Ovh-Spam-Status')?->getValue()
            ]]);
    }
}
