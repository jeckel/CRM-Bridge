<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\CommandHandler;

use App\Component\ContactManagment\Application\Adapter\ContactRepositoryAdapter;
use App\Component\ContactManagment\Application\Command\DeleteInternalContact;
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
    ) {}

    /**
     * @throws EntityNotFoundException
     */
    public function __invoke(DeleteInternalContact $command): void
    {
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
