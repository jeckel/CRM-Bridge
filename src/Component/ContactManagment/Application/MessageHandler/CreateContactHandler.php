<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\MessageHandler;

use App\Component\ContactManagment\Domain\Service\ContactProvider;
use App\Presentation\Async\Message\CreateContact;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateContactHandler
{
    public function __construct(
        private ContactProvider $contactProvider
    ) {}

    public function __invoke(CreateContact $message): void
    {
        $this->contactProvider->findOrCreate(
            firstName: $message->firstName,
            lastName: $message->lastName,
            displayName: $message->displayName,
            email: $message->email,
            phoneNumber: $message->phoneNumber,
            company: $message->company
        );
    }
}
