<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Application\AsyncHandler;

use App\Identity\AccountId;
use App\Identity\AddressBookId;
use App\Infrastructure\Doctrine\Entity\CardDavAddressBook;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use App\Presentation\Async\Message\SyncAddressBook;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Config;
use MStilkerich\CardDavClient\Services\Sync;
use MStilkerich\CardDavClient\WebDavResource;
use MStilkerich\CardDavClient\XmlElements\ElementNames;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsMessageHandler]
#[AsCronTask(
    expression: '*/5 * * * *',
    method: 'onSchedule'
)]
readonly class SyncAddressBookHandler
{
    public function __construct(
        private CardDavSyncHandler $syncHandler,
        private CardDavAddressBookRepository $addressBookRepository,
        private LoggerInterface $logger
    ) {}

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(SyncAddressBook $message): void
    {
        Config::init($this->logger, $this->logger);
        $this->syncAddressBook($this->addressBookRepository->getById($message->addressBookId));
    }

    public function onSchedule(): void
    {
        $this->syncAddressBooks();
    }

    private function syncAddressBooks(): void
    {
        $addressBooks = $this->addressBookRepository->findBy(['enabled' => true]);
        Config::init($this->logger, $this->logger);
        foreach ($addressBooks as $addressBookEntity) {
            $this->syncAddressBook($addressBookEntity);
        }
    }

    private function syncAddressBook(CardDavAddressBook $cardDavAddressBook): void
    {
        $config = $cardDavAddressBook->getCardDavConfig();
        if (null === $config) {
            return;
        }
        $account = new Account(
            discoveryUri: $config->getUri(),
            httpOptions: [
                "username" => $config->getLogin(),
                "password" => $config->getPassword()
            ]
        );

        $syncManager = new Sync();
        /** @var AddressbookCollection $addressBook */
        $addressBook = WebDavResource::createInstance(
            uri: $cardDavAddressBook->getUri(),
            account: $account,
            restype: [ElementNames::RESTYPE_ABOOK]
        );
        $this->syncHandler
            ->setAccountId(AccountId::from((string) $config->getAccountOrFail()->getId()))
            ->setAddressBookId(AddressBookId::from((string) $cardDavAddressBook->getId()));
        $syncManager->synchronize($addressBook, $this->syncHandler, [ "FN" ], "");
    }
}
