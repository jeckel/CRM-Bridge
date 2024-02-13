<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Domain\Component\DirectCommunicationHub\Service;

use App\Component\Shared\ValueObject\Email;
use App\Domain\Component\DirectCommunicationHub\Port\AuthorRepository;
use App\Domain\Component\DirectCommunicationHub\Port\IncomingMailRepository;

readonly class MailAuthorLinkManager
{
    public function __construct(
        private AuthorRepository $authorRepository,
        private IncomingMailRepository $incomingMailRepository
    ) {}

    public function linkToAuthor(Email $authorEmail): void
    {
        $author = $this->authorRepository->findByEmail($authorEmail);
        if (null === $author) {
            return;
        }
        $mails = $this->incomingMailRepository->findByAuthorEmail($authorEmail);
        foreach ($mails as $mail) {
            $mail->linkToAuthor($author);
            $this->incomingMailRepository->save($mail);
        }
    }
}
