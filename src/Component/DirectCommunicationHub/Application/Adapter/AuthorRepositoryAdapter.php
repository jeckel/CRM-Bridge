<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:59
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Adapter;

use App\Component\DirectCommunicationHub\Domain\Model\Author;
use App\Component\DirectCommunicationHub\Domain\Port\AuthorRepository;
use App\Component\Shared\Helper\ContextManager;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
use Override;

readonly class AuthorRepositoryAdapter implements AuthorRepository
{
    public function __construct(
        private ContactRepository $repository
    ) {}

    #[Override]
    public function findByEmail(Email $email): ?Author
    {
        $author = $this->repository->findByEmail((string) $email);
        if (null === $author) {
            return null;
        }
        return new Author(ContactId::from((string) $author->getId()));
    }
}
