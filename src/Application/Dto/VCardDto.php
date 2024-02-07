<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Component\ContactManagment\Port\VCard as DomainVCard;
use App\ValueObject\Email;
use Override;
use Sabre\VObject\Component\VCard;

readonly class VCardDto implements DomainVCard
{
    public function __construct(
        private string $uri,
        private string $etag,
        private VCard $card
    ) {}

    #[Override]
    public function firstName(): ?string
    {
        /** @phpstan-ignore-next-line  */
        return explode(';', (string) $this->card->N)[1];
    }

    #[Override]
    public function lastName(): ?string
    {
        /** @phpstan-ignore-next-line  */
        return explode(';', (string) $this->card->N)[0];
    }

    #[Override]
    public function displayName(): string
    {
        /** @phpstan-ignore-next-line  */
        return (string) $this->card->FN;
    }

    #[Override]
    public function email(): ?Email
    {
        /** @phpstan-ignore-next-line  */
        $email = $this->card->EMAIL;
        if (null === $email) {
            return null;
        }
        return new Email((string) $email);
    }

    #[Override]
    public function phoneNumber(): ?string
    {
        /** @phpstan-ignore-next-line  */
        return (string) $this->card->TEL;
    }

    #[Override]
    public function vCardUri(): string
    {
        return $this->uri;
    }

    #[Override]
    public function vCardEtag(): string
    {
        return $this->etag;
    }
}
