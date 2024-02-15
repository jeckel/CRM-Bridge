<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:15
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Domain\Service;

use App\Component\DirectCommunicationHub\Domain\Dto\IncomingMailDto;
use App\Component\DirectCommunicationHub\Domain\Model\IncomingMail;
use App\Component\DirectCommunicationHub\Domain\Port\AuthorRepository;
use App\Component\DirectCommunicationHub\Domain\Port\IncomingMailRepository;

readonly class IncomingMailRegisterer
{
    public function __construct(
        private AuthorRepository $authorRepository,
        private IncomingMailRepository $incomingMailRepository
    ) {}

    public function register(IncomingMailDto $incomingMailDto): void
    {
        $this->incomingMailRepository->save(
            IncomingMail::fromIncomingMailDto($incomingMailDto)
                ->linkToAuthor(
                    $this->authorRepository->findByEmail($incomingMailDto->fromAddress)
                )
        );
    }
}
