<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Application\AsyncHandler;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use App\Presentation\Async\Message\SyncAddressBook;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Services\Sync;
use MStilkerich\CardDavClient\WebDavResource;
use MStilkerich\CardDavClient\XmlElements\ElementNames;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsMessageHandler]
#[AsCronTask(
    expression: '0 * * * *',
    method: 'onSchedule'
)]
readonly class SyncAddressBookHandler
{
    public function __construct(
        private ConfigurationService $configuration,
        private AddressBookSyncHandler $syncHandler,
        private Account $account
    ) {}

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(SyncAddressBook $message): void
    {
        $this->syncAddressBook();
    }

    public function onSchedule(): void
    {
        $this->syncAddressBook();
    }

    private function syncAddressBook(): void
    {
        $addressBookUri = (string) $this->configuration->get(ConfigurationKey::CARDDAV_DEFAULT_ADDRESS_BOOK);

        $syncManager = new Sync();
        /** @var AddressbookCollection $addressBook */
        $addressBook = WebDavResource::createInstance(
            uri: $addressBookUri,
            account: $this->account,
            restype: [ElementNames::RESTYPE_ABOOK]
        );
        $syncManager->synchronize($addressBook, $this->syncHandler, [ "FN" ], "");
    }
}
