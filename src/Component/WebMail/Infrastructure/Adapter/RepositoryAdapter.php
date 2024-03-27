<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:11
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Adapter;

use App\Component\Shared\Identity\ImapAccountId;
use App\Component\WebMail\Application\Port\RepositoryPort;
use App\Component\WebMail\Domain\Entity\ImapAccount;
use App\Component\WebMail\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class RepositoryAdapter implements RepositoryPort
{
    public function __construct(private EntityManagerInterface $entityManager, private ImapAccountRepository $accountRepository) {}

    #[\Override]
    public function persistAccount(ImapAccount $account): void
    {
        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }

    #[\Override]
    public function getById(ImapAccountId $accountId): ImapAccount
    {
        return $this->accountRepository->getById($accountId);
    }
}
