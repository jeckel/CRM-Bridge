<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:11
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Adapter;

use App\Component\Shared\Identity\ImapAccountId;
use App\Component\Shared\Identity\ImapMailboxId;
use App\Component\WebMail\Application\Port\RepositoryPort;
use App\Component\WebMail\Domain\Entity\ImapAccount;
use App\Component\WebMail\Domain\Entity\ImapMail;
use App\Component\WebMail\Domain\Entity\ImapMailbox;
use App\Component\WebMail\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use App\Component\WebMail\Infrastructure\Doctrine\Repository\ImapMailboxRepository;
use App\Component\WebMail\Infrastructure\Doctrine\Repository\ImapMailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

readonly class RepositoryAdapter implements RepositoryPort
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ImapAccountRepository $accountRepository,
        private ImapMailboxRepository $mailboxRepository,
        private ImapMailRepository $mailRepository
    ) {}

    /**
     * @throws EntityNotFoundException
     */
    #[\Override]
    public function getAccountById(ImapAccountId $accountId): ImapAccount
    {
        return $this->accountRepository->getById($accountId);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[\Override]
    public function getMailboxById(ImapMailboxId $mailboxId): ImapMailbox
    {
        return $this->mailboxRepository->getById($mailboxId);
    }

    #[\Override]
    public function findMailByUniqueMessageId(string $messageUniqueId): ?ImapMail
    {
        return $this->mailRepository->findOneBy(['messageUniqueId' => $messageUniqueId]);
    }

    #[\Override]
    public function persist(ImapAccount|ImapMail|ImapMailbox $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
