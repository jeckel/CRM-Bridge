<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:11
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Adapter;

use App\Component\WebMail\Application\Port\RepositoryPort;
use App\Component\WebMail\Domain\Entity\ImapAccount;
use Doctrine\ORM\EntityManagerInterface;

readonly class RepositoryAdapter implements RepositoryPort
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[\Override]
    public function persistAccount(ImapAccount $account): void
    {
        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }
}
