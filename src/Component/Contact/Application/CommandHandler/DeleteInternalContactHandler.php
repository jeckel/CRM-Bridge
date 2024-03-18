<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Application\CommandHandler;

use App\Component\Contact\Application\Command\DeleteInternalContact;
use App\Component\Contact\Application\Port\RepositoryPort;
use App\Component\ContactManagment\Application\Adapter\ContactRepositoryAdapter;
use App\Component\Shared\Event\ContactDeleted;
use Doctrine\ORM\EntityNotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DeleteInternalContactHandler
{
    public function __construct(
        private ContactRepositoryAdapter $contactAdapter,
        private EventDispatcherInterface $eventDispatcher,
        private RepositoryPort $repository
    ) {}

    /**
     * @throws EntityNotFoundException
     */
    public function __invoke(DeleteInternalContact $command): void
    {
        $contact = $this->repository->findByVCardUri($command->vCardUri);
        $contact = $this->contactAdapter->findByVCardUri($command->vCardUri);
        if ($contact === null) {
            return;
        }
        $this->contactAdapter->delete($contact);
        $this->eventDispatcher->dispatch(
            new ContactDeleted($contact->id)
        );
    }
}
