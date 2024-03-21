<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Domain\Entity;

use App\Component\Shared\ValueObject\Email;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class ContactEmail
{
    private Email $address;

    /** @phpstan-ignore-next-line  */
    private Contact $contact;

    private ?string $type;

    private bool $isPreferred;

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function new(
        Contact $contact,
        Email $address,
        ?string $type = null,
        bool $isPreferred = false
    ): self {
        $email = new self();
        $email->address = $address;
        $email->contact = $contact;
        $email->type = $type;
        $email->isPreferred = $isPreferred;
        return $email;
    }

    public function isPrimary(): bool
    {
        return $this->isPreferred;
    }

    public function address(): Email
    {
        return $this->address;
    }

    public function type(): ?string
    {
        return $this->type;
    }
}
