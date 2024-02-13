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
use App\Component\Shared\Helper\ContextManager;
use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\Account;
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\Repository\ContactRepository as DoctrineContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

readonly class ContactRepositoryAdapter implements ContactRepository
{
    public function __construct(
        private DoctrineContactRepository $repository,
        private ContactMapper $contactMapper,
        private ContextManager $context
    ) {}

    #[\Override]
    public function save(Contact $contact): void
    {
        $entity = $this->repository->find((string) $contact->id) ?? new DoctrineContact();
        $entity = $this->contactMapper->mapToDoctrine($entity, $contact);
        $this->repository->persist($entity);
    }

    /**
     * @throws ORMException
     */
    #[\Override]
    public function findByEmail(Email $email): ?Contact
    {
        $contact = $this->repository->findOneBy([
            'email' => $email->getEmail(),
            'account' => $this->context->getAccountReference()
        ]);
        if (null === $contact) {
            return null;
        }
        return $this->contactMapper->mapToDomain($contact);
    }

    /**
     * @throws ORMException
     */
    #[\Override]
    public function findByVCard(string $vCardUri): ?Contact
    {
        $contact = $this->repository->findOneBy([
            'vCardUri' => $vCardUri,
            'account' => $this->context->getAccountReference()
        ]);
        if (null === $contact) {
            return null;
        }
        return $this->contactMapper->mapToDomain($contact);
    }
}
