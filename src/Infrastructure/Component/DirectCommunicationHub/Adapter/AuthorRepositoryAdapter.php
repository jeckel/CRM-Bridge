<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:59
 */
declare(strict_types=1);

namespace App\Infrastructure\Component\DirectCommunicationHub\Adapter;

use App\Domain\Component\DirectCommunicationHub\Model\Author;
use App\Domain\Component\DirectCommunicationHub\Port\AuthorRepository;
use App\Identity\ContactId;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
use App\ValueObject\Email;
use Override;

readonly class AuthorRepositoryAdapter implements AuthorRepository
{
    public function __construct(private ContactRepository $repository) {}

    #[Override]
    public function findByEmail(Email $email): ?Author
    {
        $contact = $this->repository->findOneBy(['email' => (string) $email]);
        if (null === $contact) {
            return null;
        }
        return new Author(ContactId::from((string) $contact->getId()));
    }
}