<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Service;

use App\Component\ContactManagment\Application\Dto\VCardDto;
use App\Component\ContactManagment\Domain\Service\ContactVCardUpdater;
use App\Component\Shared\Helper\ContextManager;
use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\Identity\AddressBookId;
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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CardDavAddressBookSynchronizer implements SyncHandler
{
    private AddressBookId $addressBookId;

    public function __construct(
        private readonly ContactVCardUpdater $VCardUpdater,
        private readonly ContextManager $context,
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
        $this->context->setAccountId(AccountId::from((string) $config->getAccountOrFail()->getId()));
        $this->addressBookId = AddressBookId::from((string) $cardDavAddressBook->getId());
        $syncManager->synchronize($addressBook, $this, [ "FN" ], "");
    }


    #[Override]
    public function addressObjectChanged(string $uri, string $etag, ?VCard $card): void
    {
        if (null !== $card) {
            $this->VCardUpdater->sync(
                vCard: new VCardDto($uri, $etag, $card),
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
