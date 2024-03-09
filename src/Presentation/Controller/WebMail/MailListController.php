<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ImapMailbox;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Infrastructure\Doctrine\Repository\ImapMailboxRepository;
use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use App\Infrastructure\Imap\ImapMailboxConnector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        //        ImapMessageRepository $repository
        //        string $folderPath,
        //        ImapAccountRepository $repository,
        ImapMailboxRepository $mailboxRepository,
        EntityManagerInterface $entityManager
    ): Response {
        //        $imapAccount = $repository->getById($imapConfigId);
        //        $mailbox = ImapMailboxConnector::fromImapAccount($imapAccount);
        //        $mailIds = $mailbox->searchFolder($folderPath);

        // @todo : move into the repository
        $mailbox = $mailboxRepository->getById($mailboxId);
        $query = $entityManager->createQueryBuilder();
        $mails = $query->select('m.id, m.subject, m.fromName, m.fromAddress, m.date, m.isTreated, m.spamHeaders, c.id as authorId, c.displayName as authorName')
            ->from(ImapMessage::class, 'm')
            ->leftJoin(Contact::class, 'c', 'WITH', 'c.id = m.contact')
            ->where('m.imapMailbox = :imapMailbox')
//            ->andWhere('m.folder = :folder')
//            ->andWhere('m.uid IN (:mailIds)')
            ->setParameter(':imapMailbox', $mailbox)
//            ->setParameter(':folder', $folderPath)
//            ->setParameter(':mailIds', $mailIds)
            ->orderBy('m.date', 'DESC')
            ->getQuery()
            ->execute();


        return $this->render(
            'webmail/mail_list.html.twig',
            [
//                'imapConfigId' => $imapConfigId,
//                'folder' => $folderPath,
//                'mailIds' => $mailIds,
                'mailbox' => $mailbox,
                'mails' => $mails,
            ]
        );
    }
}
