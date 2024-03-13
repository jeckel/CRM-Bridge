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
use App\Infrastructure\Doctrine\Repository\ImapMailboxRepository;
use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use App\Infrastructure\Imap\Mail\MailProvider;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use ZBateson\MailMimeParser\Message;

#[Route(
    path: "/webmail",
    name: "webmail.",
)]
class IndexController extends AbstractController
{
    public function __construct(
        private readonly ImapAccountRepository $imapAccountRepo
    ) {}

    #[Route(
        path: "/",
        name: "index",
        methods: ['GET']
    )]
    public function index(): Response
    {
        if ($this->imapAccountRepo->count([]) === 0) {
            $this->addFlash('error', 'setup.flash_message.imap_account_required');
            return $this->redirectToRoute('setup.index');
        }
        return $this->render(
            'pages/webmail/index.html.twig',
        );
    }

    #[Route(
        path: '/mailboxes',
        name: 'mailboxes',
        methods: ['GET'],
        priority: 2
    )]
    public function mailboxes(): Response
    {
        return $this->render(
            'pages/webmail/mailboxes_embed.html.twig',
            ['imapAccounts' => $this->imapAccountRepo->findAll()]
        );
    }

    #[Route(
        path: '/mailbox/{mailboxId}',
        name: 'list_mails',
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
            'pages/webmail/list_mails_embed.html.twig',
            [
                'title' => $mailbox->getName(),
                'mails' => $mails,
                'page' => $page,
                'limit' => $limit,
                'total' => $mails->getTotalItemCount()
            ]
        );
    }

    #[Route(
        path: '/contact/{contactId}/mails',
        name: 'contact.mails'
    )]
    public function contactMails(
        string $contactId,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 20;
        $mails = $paginator->paginate(
            $entityManager->createQueryBuilder()
                ->select('m.id, m.subject, m.fromName, m.fromAddress, m.date, m.isTreated, m.spamHeaders, c.id as authorId, c.displayName as authorName')
                ->from(ImapMessage::class, 'm')
                ->innerJoin(Contact::class, 'c', 'WITH', 'c.id = m.contact')
                ->where('c.id = :contactId')
                ->setParameter(':contactId', $contactId)
                ->orderBy('m.date', 'DESC'),
            $page,
            $limit
        );
        return $this->render(
            'pages/webmail/list_mails_embed.html.twig',
            [
                'mails' => $mails,
                'page' => $page,
                'limit' => $limit,
                'total' => $mails->getTotalItemCount()
            ]
        );
    }

    #[Route(
        path: '/mail/{mailId}',
        name: 'mail.details',
        methods: ['GET']
    )]
    public function details(string $mailId, ImapMessageRepository $repository): Response
    {
        $mail = $repository->getById($mailId);
        return $this->render('pages/webmail/mail_embed.html.twig', ['mail' => $mail]);
    }

    #[Route(
        path: '/mail/{mailId}/debug',
        name: 'mail.debug',
        methods: ['GET']
    )]
    public function debug(string $mailId, ImapMessageRepository $repository, MailProvider $provider): Response
    {
        $entityMail = $repository->getById($mailId);
        $fullMail = $provider->getMail($entityMail->getImapAccountOrFail(), $entityMail->getImapPath(), $entityMail->getImapUid());
        $fullMail->getImapMail();
        $parsedHeaders = Message::from($fullMail->getEntity()->getHeaderRaw(), true);

        return $this->render(
            'debug/dump_var.html.twig',
            ['var' => [
                'original' => $fullMail,
                'parsed' => $parsedHeaders,
                'spam' => $parsedHeaders->getHeader('X-Ovh-Spam-Status')?->getValue()
            ]]
        );
    }
}
