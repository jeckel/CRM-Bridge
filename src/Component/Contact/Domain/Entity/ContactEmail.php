<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Domain\Entity;

use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\Contact;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class ContactEmail
{
    private Email $emailAddress;

    /** @phpstan-ignore-next-line  */
    private Contact $contact;

    private string $emailType;

    public static function new(
        Email $emailAddress,
        Contact $contact,
        string $emailType = 'Work'
    ): self {
        $email = new self();
        $email->emailAddress = $emailAddress;
        $email->contact = $contact;
        $email->emailType = $emailType;
        return $email;
    }

    public function isPrimary(): bool
    {
        return $this->emailType === 'Work';
    }

    public function address(): Email
    {
        return $this->emailAddress;
    }

    public function type(): string
    {
        return $this->emailType;
    }
}
