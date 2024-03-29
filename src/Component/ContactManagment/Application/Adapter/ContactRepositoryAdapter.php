<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Adapter;

use App\Component\ContactManagment\Application\Mapper\ContactMapper;
use App\Component\ContactManagment\Domain\Entity\Contact;
use App\Component\ContactManagment\Domain\Port\ContactRepository;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\Repository\ContactRepository as DoctrineContactRepository;
use Doctrine\ORM\EntityNotFoundException;
use Override;
use Ramsey\Uuid\Uuid;

readonly class ContactRepositoryAdapter implements ContactRepository
{
    public function __construct(
        private DoctrineContactRepository $repository,
        private ContactMapper $contactMapper
    ) {}

    #[Override]
    public function save(Contact $contact): void
    {
        $entity = $this->repository->find(Uuid::fromString((string) $contact->id)) ?? new DoctrineContact();
        $entity = $this->contactMapper->mapToDoctrine($entity, $contact);
        $this->repository->persist($entity);
    }

    #[Override]
    public function findByEmail(Email $email): ?Contact
    {
        $contact = $this->repository->findByEmail((string) $email);
        if (null === $contact) {
            return null;
        }
        return $this->contactMapper->mapToDomain($contact);
    }

    #[Override]
    public function findByVCard(string $vCardUri): ?Contact
    {
        $contact = $this->repository->findOneBy([
            'vCardUri' => $vCardUri
        ]);
        if (null === $contact) {
            return null;
        }
        return $this->contactMapper->mapToDomain($contact);
    }

    #[Override]
    public function getById(ContactId $contactId): Contact
    {
        return $this->contactMapper->mapToDomain(
            $this->repository->getById((string) $contactId)
        );
    }

    public function deleteByVCardUri(string $vCardUri): void
    {
        $contact = $this->repository->findOneBy([
            'vCardUri' => $vCardUri
        ]);
        if (null !== $contact) {
            $this->repository->remove($contact);
        }
    }

    public function findByVCardUri(string $vCardUri): ?Contact
    {
        $entity = $this->repository->findOneBy([
            'vCardUri' => $vCardUri
        ]);
        if (null === $entity) {
            return null;
        }
        return $this->contactMapper->mapToDomain($entity);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(Contact $contact): void
    {
        $entity = $this->repository->getById($contact->id);
        $this->repository->remove($entity);
    }
}
