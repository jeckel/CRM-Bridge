<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Infrastructure\Doctrine\Adapter;

use App\Component\CardDav\Application\Port\RepositoryPort;
use App\Component\CardDav\Domain\Entity\CardDavAccount;
use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\CardDav\Infrastructure\Doctrine\Repository\CardDavAccountRepository;
use App\Component\CardDav\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\CardDavAddressBookId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Override;

readonly class RepositoryAdapter implements RepositoryPort
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    #[Override]
    public function persist(CardDavAccount|CardDavAddressBook $entity): void
    {
        $this->entityManager->persist($entity);
    }

    #[Override]
    public function flush(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Override]
    public function getAccountById(CardDavAccountId $accountId): CardDavAccount
    {
        /** @var CardDavAccountRepository $repository */
        $repository = $this->entityManager->getRepository(CardDavAccount::class);
        return $repository->getById($accountId);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAddressBooksByAccount(CardDavAccountId $accountId): iterable
    {
        /** @var CardDavAddressBookRepository $repository */
        $repository = $this->entityManager->getRepository(CardDavAddressBook::class);
        return $repository->findBy(['account' => $accountId]);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Override]
    public function getAddressBookById(CardDavAddressBookId $addressBookId): CardDavAddressBook
    {
        /** @var CardDavAddressBookRepository $repository */
        $repository = $this->entityManager->getRepository(CardDavAddressBook::class);
        return $repository->getById($addressBookId);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getEnabledAddressBooks(): iterable
    {
        /** @var CardDavAddressBookRepository $repository */
        $repository = $this->entityManager->getRepository(CardDavAddressBook::class);
        return $repository->findBy(['enabled' => true]);
    }

    #[Override]
    public function findAddressBookByUri(string $addressBookUri, CardDavAccountId $accountId): ?CardDavAddressBook
    {
        /** @var CardDavAddressBookRepository $repository */
        $repository = $this->entityManager->getRepository(CardDavAddressBook::class);
        return $repository->findOneBy([
            'uri' => $addressBookUri,
            'account' => $accountId,
        ]);
    }
}
