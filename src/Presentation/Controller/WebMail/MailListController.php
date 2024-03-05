<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Infrastructure\Imap\ImapMailboxConnector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MailListController extends AbstractController
{
    #[Route(
        path: '/webmail/account/{imapConfigId}/folder/{folderPath}',
        name: 'webmail_list',
        methods: ['GET']
    )]
    public function listMails(
        string $imapConfigId,
        string $folderPath,
        ImapAccountRepository $repository,
        EntityManagerInterface $entityManager
    ): Response {
        $imapAccount = $repository->getById($imapConfigId);
        $mailbox = ImapMailboxConnector::fromImapAccount($imapAccount);
        $mailIds = $mailbox->searchFolder($folderPath);

        // @todo : move into the repository
        $query = $entityManager->createQueryBuilder();
        $mails = $query->select('m.id, m.subject, m.fromName, m.fromAddress, m.date, m.isTreated, c.id as authorId, c.displayName as authorName')
            ->from(ImapMessage::class, 'm')
            ->leftJoin(Contact::class, 'c', 'WITH', 'c.id = m.contact')
            ->where('m.imapAccount = :imapAccount')
            ->andWhere('m.folder = :folder')
            ->andWhere('m.uid IN (:mailIds)')
            ->setParameter(':imapAccount', $imapAccount)
            ->setParameter(':folder', $folderPath)
            ->setParameter(':mailIds', $mailIds)
            ->orderBy('m.date', 'DESC')
            ->getQuery()
            ->execute();


        return $this->render(
            'webmail/mail_list.html.twig',
            [
                'imapConfigId' => $imapConfigId,
                'folder' => $folderPath,
//                'mailIds' => $mailIds,
                'mails' => $mails,
            ]
        );
    }
}
