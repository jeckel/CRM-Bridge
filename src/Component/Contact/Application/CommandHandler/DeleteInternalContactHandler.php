<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Application\CommandHandler;

use App\Component\Contact\Application\Command\DeleteInternalContact;
use App\Component\Contact\Application\Port\RepositoryPort;
use App\Component\Shared\Event\ContactDeleted;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteInternalContactHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private RepositoryPort $repository
    ) {}

    public function __invoke(DeleteInternalContact $command): void
    {
        $contact = $this->repository->findByVCardUri($command->vCardUri);
        if ($contact === null) {
            return;
        }
        $this->repository->delete($contact);
        $this->eventDispatcher->dispatch(
            new ContactDeleted($contact->id()),
        );
    }
}
