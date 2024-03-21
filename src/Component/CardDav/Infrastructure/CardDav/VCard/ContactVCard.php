<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 14/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Infrastructure\CardDav\VCard;

use App\Component\CardDav\Infrastructure\CardDav\InvalidArgumentException;
use App\Component\Shared\ValueObject\Email;
use Exception;
use Sabre\VObject\Component\VCard;
use Sabre\VObject\Property\FlatText;

/**
 * @see https://en.wikipedia.org/wiki/VCard#Properties
 */
readonly class ContactVCard
{
    public function __construct(
        public string $vCardUri,
        private VCard $vCard
    ) {}

    //    public function uid(): string
    //    {
    //        dd($this->vCard->UID);
    //        return (string) $this->vCard->UID;
    //    }

    public function hasPhoto(): bool
    {
        /** @phpstan-ignore-next-line  */
        return isset($this->vCard->PHOTO);
    }

    public function displayName(): string
    {
        /** @phpstan-ignore-next-line  */
        return (string) $this->vCard->FN;
    }

    public function name(): ComposedName
    {
        return new ComposedName(
            ...array_combine(
                ['familyName', 'firstName', 'middleName', 'honorificPrefix', 'honorificSuffix'],
                /** @phpstan-ignore-next-line  */
                $this->vCard->N->getParts()
            )
        );
    }

    public function email(): ?Email
    {
        /** @phpstan-ignore-next-line  */
        $email = $this->vCard->EMAIL;
        if (null === $email) {
            return null;
        }
        try {
            return new Email((string) $email);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @return array{email: Email, type: ?string, pref: bool}[]
     */
    public function emails(): array
    {
        $emails = [];
        /** @var FlatText $email */
        /** @phpstan-ignore-next-line  */
        foreach($this->vCard->EMAIL as $email) {
            /** @phpstan-ignore-next-line  */
            $isPref = ((string) $email->offsetGet('TYPE')) === 'pref';

            /** @phpstan-ignore-next-line  */
            $type = $email->offsetExists('TYPE') ? (string) $email->offsetGet('TYPE') : null;
            try {
                $emails[] = [
                    'email' => new Email($email->getValue()),
                    'type' => $type,
                    'pref' => $isPref,
                ];
            } catch (InvalidArgumentException) {
                continue;
            }
        }
        return $emails;
    }

    public function phoneNumber(): ?string
    {
        /** @phpstan-ignore-next-line  */
        $tel = $this->vCard->TEL;
        return null === $tel ? null : (string) $tel;
    }

    public function company(): ?string
    {
        /** @phpstan-ignore-next-line  */
        $company = trim(explode(';', (string) $this->vCard->ORG)[0]);
        return $company === '' ? null : $company;
    }

    /**
     * @return string[]
     */
    public function categories(): array
    {
        /** @phpstan-ignore-next-line  */
        return explode(',', (string) $this->vCard->CATEGORIES);
    }

    /**
     * @throws Exception
     */
    public function getPhotoMimeType(): string
    {
        if (! $this->hasPhoto()) {
            throw new InvalidArgumentException('No photo in vCard');
        }
        /** @phpstan-ignore-next-line  */
        return 'image/' . $this->vCard->PHOTO['TYPE'];
    }

    /**
     * @throws Exception
     */
    public function getPhotoContent(): string
    {
        if (! $this->hasPhoto()) {
            throw new InvalidArgumentException('No photo in vCard');
        }
        /** @phpstan-ignore-next-line  */
        return (string) $this->vCard->PHOTO;
    }
}
