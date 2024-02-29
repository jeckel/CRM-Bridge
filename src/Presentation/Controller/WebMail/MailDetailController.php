<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

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
}
