<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\MessageHandler;

use App\Component\ContactManagment\Domain\Service\ContactProvider;
use App\Component\Shared\Helper\ContextManager;
use App\Presentation\Async\Message\CreateContact;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateContactHandler
{
    public function __construct(
        private ContactProvider $contactProvider,
        private ContextManager $context,
    ) {}

    public function __invoke(CreateContact $message): void
    {
        $this->context->setAccountId($message->accountId);
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
