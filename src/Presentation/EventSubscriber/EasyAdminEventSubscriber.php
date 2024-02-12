<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\EventSubscriber;

use App\Infrastructure\CardDav\AddressBookDiscovery;
use App\Infrastructure\Doctrine\Entity\CardDavAddressBook;
use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function App\new_uuid;

readonly class EasyAdminEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AddressBookDiscovery $addressBookDiscovery,
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
        if ($instance instanceof CardDavConfig) {
            foreach($this->addressBookDiscovery->discoverAddressBooks($instance) as $addressBook) {
                if (null !== $this->addressBookRepository->findOneBy([
                        'uri' => $addressBook->getUri(),
                        'cardDavConfig' => $instance,
                    ])) {
                    continue;
                }
                $entity = (new CardDavAddressBook())
                    ->setId(new_uuid())
                    ->setName($addressBook->getName())
                    ->setUri($addressBook->getUri())
                    ->setCardDavConfig($instance);
                $this->addressBookRepository->persist($entity);
            }
        }
    }
}
