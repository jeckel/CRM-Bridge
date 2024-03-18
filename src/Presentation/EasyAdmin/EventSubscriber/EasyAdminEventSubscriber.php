<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\EasyAdmin\EventSubscriber;

use App\Component\CardDav\Domain\Entity\CardDavAccount;
use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Infrastructure\CardDav\CardDavClientProvider;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class EasyAdminEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CardDavClientProvider $cardDavClientProvider,
        private CardDavAddressBookRepository $addressBookRepository
    ) {}


    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'onAfterEntityPersisted',
        ];
    }

    public function onAfterEntityPersisted(AfterEntityPersistedEvent $event): void
    {
        $instance = $event->getEntityInstance();
        if ($instance instanceof CardDavAccount) {
            foreach($this->cardDavClientProvider->getClient($instance)->discoverAddressBooks() as $addressBook) {
                if (null !== $this->addressBookRepository->findOneBy([
                        'uri' => $addressBook->getUri(),
                        'cardDavConfig' => $instance,
                    ])) {
                    continue;
                }
                $entity = CardDavAddressBook::new(
                    $addressBook->getName(),
                    $addressBook->getUri(),
                    $instance
                );
                $this->addressBookRepository->persist($entity);
            }
        }
    }
}
