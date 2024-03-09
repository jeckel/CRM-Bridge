<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use App\Infrastructure\Doctrine\Repository\ImapMailboxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MailListController extends AbstractController
{
    #[Route(
        path: '/webmail/mailbox/{mailboxId}',
        name: 'webmail_list',
        methods: ['GET']
    )]
    public function listMails(
        string $mailboxId,
        ImapMailboxRepository $mailboxRepository,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $mailbox = $mailboxRepository->getById($mailboxId);
        $query = $entityManager->createQueryBuilder();

        $page = $request->query->getInt('page', 1);
        $limit = 20;
        $mails = $paginator->paginate(
            $query->select('m.id, m.subject, m.fromName, m.fromAddress, m.date, m.isTreated, m.spamHeaders, c.id as authorId, c.displayName as authorName')
                ->from(ImapMessage::class, 'm')
                ->leftJoin(Contact::class, 'c', 'WITH', 'c.id = m.contact')
                ->where('m.imapMailbox = :imapMailbox')
                ->setParameter(':imapMailbox', $mailbox)
                ->orderBy('m.date', 'DESC'),
            $page,
            $limit
        );

        return $this->render(
            'webmail/mail_list.html.twig',
            [
                'mailbox' => $mailbox,
                'mails' => $mails,
                'page' => $page,
                'limit' => $limit,
                'total' => $mails->getTotalItemCount()
            ]
        );
    }
}
