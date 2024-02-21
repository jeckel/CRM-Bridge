<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\CommandHandler;

use App\Component\ContactManagment\Application\Command\AddEmailAddress;
use App\Component\ContactManagment\Domain\Port\ContactRepository;
use App\Component\Shared\Event\DomainEventCollection;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class AddEmailAddressHandler
{
    public function __construct(
        private ContactRepository $repository,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function __invoke(AddEmailAddress $command): void
    {
        $contact = $this->repository->getById($command->contactId)
            ->addEmailAddress($command->emailAddress);
        $this->repository->save($contact);

        foreach (DomainEventCollection::getInstance()->popEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }
}
