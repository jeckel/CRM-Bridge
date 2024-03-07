<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MailboxListController extends AbstractController
{
    #[Route(
        path: '/webmail/mailboxes',
        name: 'webmail_mailboxes',
        methods: ['GET'],
        priority: 2
    )]
    public function folders(ImapAccountRepository $imapAccountRepo): Response
    {
        return $this->render(
            'webmail/mailboxes.html.twig',
            ['imapAccounts' => $imapAccountRepo->findAll()]
        );
    }
}
