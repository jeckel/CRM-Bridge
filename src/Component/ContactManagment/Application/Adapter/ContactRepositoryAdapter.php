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
use Override;

readonly class ContactRepositoryAdapter implements ContactRepository
{
    public function __construct(
        private DoctrineContactRepository $repository,
        private ContactMapper $contactMapper
    ) {}

    #[Override]
    public function save(Contact $contact): void
    {
        $entity = $this->repository->find((string) $contact->id) ?? new DoctrineContact();
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
}
