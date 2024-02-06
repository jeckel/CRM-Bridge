<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:15
 */
declare(strict_types=1);

namespace App\Domain\Component\DirectCommunicationHub\Service;

use App\Domain\Component\DirectCommunicationHub\Dto\IncomingMailDto;
use App\Domain\Component\DirectCommunicationHub\Model\IncomingMail;
use App\Domain\Component\DirectCommunicationHub\Port\AuthorRepository;
use App\Domain\Component\DirectCommunicationHub\Port\IncomingMailRepository;

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
