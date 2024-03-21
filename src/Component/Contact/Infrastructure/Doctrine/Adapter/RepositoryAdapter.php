<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Infrastructure\Doctrine\Adapter;

use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\Contact\Application\Port\RepositoryPort;
use App\Component\Contact\Domain\Entity\Company;
use App\Component\Contact\Domain\Entity\Contact;
use App\Component\Contact\Infrastructure\Doctrine\Repository\CompanyRepository;
use App\Component\Contact\Infrastructure\Doctrine\Repository\ContactRepository;
use App\Component\Shared\Identity\CardDavAddressBookId;
use Doctrine\ORM\EntityManagerInterface;

class RepositoryAdapter implements RepositoryPort
{
    private ContactRepository $contactRepo;
    private CompanyRepository $companyRepo;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    private function contactRepo(): ContactRepository
    {
        if (! isset($this->contactRepo)) {
            /** @var ContactRepository $repo */
            $repo = $this->entityManager->getRepository(Contact::class);
            $this->contactRepo = $repo;
        }
        return $this->contactRepo;
    }

    private function companyRepo(): CompanyRepository
    {
        if (! isset($this->companyRepo)) {
            /** @var CompanyRepository $repo */
            $repo = $this->entityManager->getRepository(Company::class);
            $this->companyRepo = $repo;
        }
        return $this->companyRepo;
    }

    #[\Override]
    public function findByVCardUri(string $vCardUri): ?Contact
    {
        return $this->contactRepo()->findOneBy(['vCardUri' => $vCardUri]);
    }

    #[\Override]
    public function delete(Contact $entity): void
    {
        $this->entityManager->remove($entity);
    }

    #[\Override]
    public function flush(): void
    {
        $this->entityManager->flush();
    }

    #[\Override]
    public function persist(Contact $contact): void
    {
        $this->entityManager->persist($contact);
    }

    #[\Override]
    public function getAddressBookReference(CardDavAddressBookId $addressBookId): CardDavAddressBook
    {
        /** @var CardDavAddressBook $ref */
        $ref = $this->entityManager->getReference(CardDavAddressBook::class, $addressBookId);
        return $ref;
    }

    #[\Override]
    public function findCompanyBySlug(string $companySlug): ?Company
    {
        return $this->companyRepo()->findOneBy(['slug' => $companySlug]);
    }
}
