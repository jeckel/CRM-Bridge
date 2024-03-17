<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Command;

use App\Component\Shared\Identity\CardDavAddressBookId;
use App\Component\Shared\ValueObject\Email;
use Sabre\VObject\Component\VCard;

readonly class UpsertInternalContact
{
    public function __construct(
        public string $vCardUri,
        public string $vCardEtag,
        public VCard $card,
        public CardDavAddressBookId $addressBookId
    ) {}

    public function firstName(): ?string
    {
        /** @phpstan-ignore-next-line  */
        return explode(';', (string) $this->card->N)[1];
    }

    public function lastName(): ?string
    {
        /** @phpstan-ignore-next-line  */
        return explode(';', (string) $this->card->N)[0];
    }

    public function displayName(): string
    {
        /** @phpstan-ignore-next-line  */
        return (string) $this->card->FN;
    }

    public function email(): ?Email
    {
        /** @phpstan-ignore-next-line  */
        $email = $this->card->EMAIL;
        if (null === $email) {
            return null;
        }
        return new Email((string) $email);
    }

    public function phoneNumber(): ?string
    {
        /** @phpstan-ignore-next-line  */
        return (string) $this->card->TEL;
    }

    public function company(): ?string
    {
        /** @phpstan-ignore-next-line  */
        $company = trim(explode(';', (string) $this->card->ORG)[0]);
        return $company === '' ? null : $company;
    }

    public function vCardEtag(): string
    {
        return $this->vCardEtag;
    }
}
