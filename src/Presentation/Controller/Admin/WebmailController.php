<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 25/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\ImapConfig;
use App\Infrastructure\Doctrine\Repository\ImapConfigRepository;
use App\Infrastructure\Imap\ImapMailbox;
use App\Infrastructure\Imap\MailProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebmailController extends AbstractController
{
    #[Route(
        path: "/webmail/",
        name: "webmail_index",
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render(
            'webmail/index.html.twig',
            [
            ]
        );
    }

    #[Route(
        path: '/webmail/folders',
        name: 'webmail_folders',
        methods: ['GET']
    )]
    public function folders(EntityManagerInterface $em): Response
    {
        $imaps = $em->getRepository(ImapConfig::class)->findAll();
        $data = [];
        foreach ($imaps as $imap) {
            $mailbox = ImapMailbox::fromImapConfig($imap);
            $folders = $mailbox->listFolders();
            $data[] = [
                'imap' => $imap,
                'folders' => $folders
            ];
        }

        return $this->render(
            'webmail/folders.html.twig',
            ['imapAccounts' => $data]
        );
    }

    #[Route(
        path: '/webmail/account/{imapConfigId}/folder/{folderPath}',
        name: 'webmail_list',
        methods: ['GET']
    )]
    public function listMails(
        string $imapConfigId,
        string $folderPath,
        ImapConfigRepository $repository
    ): Response {
        $imapConfig = $repository->getById($imapConfigId);
        $mailbox = ImapMailbox::fromImapConfig($imapConfig);
        $mailIds = $mailbox->searchFolder($folderPath);
        return $this->render(
            'webmail/mail_list.html.twig',
            [
                'imapConfigId' => $imapConfigId,
                'folder' => $folderPath,
                'mailIds' => $mailIds
            ]
        );
    }

    #[Route(
        path: '/webmail/account/{imapConfigId}/{folder}/{mailId}/row',
        name: 'webmail_mail_row',
        methods: ['GET']
    )]
    public function mailRow(
        string $imapConfigId,
        string $folder,
        int $mailId,
        ImapConfigRepository $repository,
        MailProvider $mailProvider
    ): Response {
        $imapConfig = $repository->getById($imapConfigId);
//        $mailbox = ImapMailbox::fromImapConfig($imapConfig);
//        $mail = $mailbox->getMail($mailId, $folder);

        return $this->render(
            'webmail/mail_row.html.twig',
            [
                'imapConfigId' => $imapConfigId,
                'mailId' => $mailId,
                'mail' => $mailProvider->getMail($mailId, $folder, $imapConfig),
            ]
        );
    }
}
