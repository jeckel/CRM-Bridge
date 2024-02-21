<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\CommandHandler;

use App\Component\ContactManagment\Application\Command\UpsertContact;
use App\Component\ContactManagment\Application\Service\UpsertContactManager;
use App\Component\Shared\Event\DomainEventCollection;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UpsertContactHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private UpsertContactManager $upsertContactManager,
    ) {}

    public function __invoke(UpsertContact $command): void
    {
        $this->upsertContactManager->upsertContact(
            data: $command->contactData,
            contactId: $command->contactId
        );

        foreach (DomainEventCollection::getInstance()->popEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
