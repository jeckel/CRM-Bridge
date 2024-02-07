<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Component\ContactManagment\Adapter;

use App\Domain\Component\ContactManagment\Entity\Contact;
use App\Domain\Component\ContactManagment\Port\ContactRepository;
use App\Infrastructure\Component\ContactManagment\Mapper\ContactMapper;
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\Repository\ContactRepository as DoctrineContactRepository;
use App\ValueObject\Email;

readonly class ContactRepositoryAdapter implements ContactRepository
{
    public function __construct(
        private DoctrineContactRepository $repository,
        private ContactMapper $contactMapper
    ) {}

    #[\Override]
    public function save(Contact $contact): void
    {
        $entity = $this->repository->find((string) $contact->id) ?? new DoctrineContact();
        $entity = $this->contactMapper->mapToDoctrine($entity, $contact);
        $this->repository->persist($entity);
    }

    #[\Override]
    public function findByEmail(Email $email): ?Contact
    {
        $contact = $this->repository->findOneBy(['email' => $email->getEmail()]);
        if (null === $contact) {
            return null;
        }
        return $this->contactMapper->mapToDomain($contact);
    }

    #[\Override]
    public function findByVCard(string $vCardUri): ?Contact
    {
        $contact = $this->repository->findOneBy(['vCardUri' => $vCardUri]);
        if (null === $contact) {
            return null;
        }
        return $this->contactMapper->mapToDomain($contact);
    }
}
