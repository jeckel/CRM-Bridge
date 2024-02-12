<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Dto\VCardDto;
use App\Domain\Component\ContactManagment\Service\ContactVCardUpdater;
use App\Identity\AccountId;
use App\Identity\AddressBookId;
use App\Infrastructure\Doctrine\Entity\CardDavAddressBook;
use MStilkerich\CardDavClient\Account;
use MStilkerich\CardDavClient\AddressbookCollection;
use MStilkerich\CardDavClient\Config;
use MStilkerich\CardDavClient\Services\Sync;
use MStilkerich\CardDavClient\Services\SyncHandler;
use MStilkerich\CardDavClient\WebDavResource;
use MStilkerich\CardDavClient\XmlElements\ElementNames;
use Override;
use Psr\Log\LoggerInterface;
use Sabre\VObject\Component\VCard;

class CardDavAddressBookSynchronizer implements SyncHandler
{
    private AccountId $accountId;
    private AddressBookId $addressBookId;

    public function __construct(
        private readonly ContactVCardUpdater $VCardUpdater,
        LoggerInterface $logger
    ) {
        Config::init($logger, $logger);
    }

    public function syncAddressBook(CardDavAddressBook $cardDavAddressBook): void
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
        $this->accountId = AccountId::from((string) $config->getAccountOrFail()->getId());
        $this->addressBookId = AddressBookId::from((string) $cardDavAddressBook->getId());
        $syncManager->synchronize($addressBook, $this, [ "FN" ], "");
    }


    #[Override]
    public function addressObjectChanged(string $uri, string $etag, ?VCard $card): void
    {
        if (null !== $card) {
            $this->VCardUpdater->sync(
                vCard: new VCardDto($uri, $etag, $card),
                accountId: $this->accountId,
                addressBookId: $this->addressBookId
            );
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function addressObjectDeleted(string $uri): void
    {
        // TODO: Implement addressObjectDeleted() method.
    }

    #[Override]
    public function getExistingVCardETags(): array
    {
        return [];
    }

    #[Override]
    public function finalizeSync(): void
    {
        // TODO: Implement finalizeSync() method.
    }
}
