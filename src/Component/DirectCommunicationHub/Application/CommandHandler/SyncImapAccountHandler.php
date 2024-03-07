<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/03/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\CommandHandler;

use App\Component\DirectCommunicationHub\Application\Command\SyncImapAccount;
use App\Component\DirectCommunicationHub\Application\Command\SyncImapMailbox;
use App\Infrastructure\Doctrine\Entity\ImapMailbox;
use App\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Infrastructure\Doctrine\Repository\ImapMailboxRepository;
use App\Infrastructure\Imap\ImapMailboxConnector;
use InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

use function App\slug;

#[AsMessageHandler]
readonly class SyncImapAccountHandler
{
    public function __construct(
        private ImapAccountRepository $accountRepo,
        private ImapMailboxRepository $folderRepo,
        private MessageBusInterface $messageBus
    ) {}

    public function __invoke(SyncImapAccount $message): void
    {
        $imapAccount = $this->accountRepo->getById($message->imapAccountId);
        $mailboxes = ImapMailboxConnector::fromImapAccount($imapAccount)->listFolders();
        foreach($mailboxes as $imapPath) {
            $folderEntity = $this->folderRepo->findOneBy([
                'imapAccount' => $imapAccount,
                'imapPath' => $imapPath
            ]);
            if (null === $folderEntity) {
                $shortPath = $this->shortPath($imapPath);
                $folderEntity = (new ImapMailbox())
                    ->setImapAccount($imapAccount)
                    ->setName($shortPath)
                    ->setImapPath($imapPath)
                    ->setSlug(slug($shortPath))
                ;
                $this->folderRepo->persist($folderEntity);
            }
            if ($folderEntity->isEnabled()) {
                $this->messageBus->dispatch(
                    new SyncImapMailbox($folderEntity->getIdentity())
                );
            }
        }
    }

    public function shortPath(string $imapPath): string
    {
        $pos = strpos($imapPath, '}');
        if (false === $pos) {
            throw new InvalidArgumentException('Invalid imap path');
        }
        return substr($imapPath, $pos + 1);
    }
}
