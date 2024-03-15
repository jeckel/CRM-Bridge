<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EntityModel;

use App\Component\Shared\ValueObject\Email;
use App\Component\Shared\ValueObject\EmailType;
use App\Infrastructure\Doctrine\Entity\Contact;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class ContactEmail
{
    private Email $emailAddress;

    /** @phpstan-ignore-next-line  */
    private Contact $contact;

    private EmailType $emailType;

    public static function new(
        Email $emailAddress,
        Contact $contact,
        EmailType $emailType = EmailType::PRIMARY
    ): self {
        $email = new self();
        $email->emailAddress = $emailAddress;
        $email->contact = $contact;
        $email->emailType = $emailType;
        return $email;
    }

    public function isPrimary(): bool
    {
        return $this->emailType === EmailType::PRIMARY;
    }

    public function address(): Email
    {
        return $this->emailAddress;
    }

    public function type(): EmailType
    {
        return $this->emailType;
    }
}
