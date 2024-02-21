<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\CommandHandler;

use App\Component\ContactManagment\Application\Command\UpsertContactVCard;
use App\Component\ContactManagment\Application\Dto\ContactDto;
use App\Component\ContactManagment\Application\Service\UpsertContactManager;
use App\Component\ContactManagment\Domain\Entity\Contact;
use App\Component\ContactManagment\Domain\Port\ContactRepository;
use App\Component\Shared\Event\DomainEventCollection;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UpsertContactVCardHandler
{
    public function __construct(
        private ContactRepository $repository,
        private EventDispatcherInterface $eventDispatcher,
        private UpsertContactManager $upsertContactManager,
        private Clock $clock
    ) {}

    public function __invoke(UpsertContactVCard $command): void
    {
        $contact = $this->repository->findByVCard($command->vCardUri);
        $contact = $this->upsertContactManager->upsertContact(
            data: new ContactDto(
                displayName: $command->displayName(),
                firstName: $command->firstName(),
                lastName: $command->lastName(),
                emailAddress: $command->email(),
                phoneNumber: $command->phoneNumber(),
                company: $command->company()
            ),
            contact: $contact
        );

        $contact = $this->upsertLinkToVCard(
            $contact,
            $command
        );

        $this->repository->save($contact);

        foreach (DomainEventCollection::getInstance()->popEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }
    }

    protected function upsertLinkToVCard(
        Contact $contact,
        UpsertContactVCard $command
    ): Contact {
        return $contact->linkVCard(
            vCardUri: $command->vCardUri,
            vCardEtag: $command->vCardEtag,
            vCardLastSyncAt: $this->clock->now(),
            addressBookId: $command->addressBookId
        );
    }
}
