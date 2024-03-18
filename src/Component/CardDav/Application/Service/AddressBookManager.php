<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Service;

use App\Component\CardDav\Domain\Entity\CardDavAccount;
use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Infrastructure\CardDav\CardDavClientProvider;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class AddressBookManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CardDavClientProvider $cardDavClientProvider,
        private CardDavAddressBookRepository $addressBookRepository,
    ) {}

    public function fetchAddressBookFromAccount(CardDavAccount $account): void
    {
        foreach (
            $this->cardDavClientProvider->getClient(
                $account
            )->discoverAddressBooks() as $addressBook
        ) {
            if (null !== $this->addressBookRepository->findOneBy([
                    'uri' => $addressBook->getUri(),
                    'account' => $account,
                ])) {
                continue;
            }
            $entity = CardDavAddressBook::new(
                $addressBook->getName(),
                $addressBook->getUri(),
                $account
            );
            $this->entityManager->persist($entity);
        }
    }
}
