<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Dto;

use App\Component\Shared\ValueObject\Email;
use InvalidArgumentException;

class ContactDto
{
    public function __construct(
        public string $displayName,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?Email $emailAddress = null,
        public ?string $phoneNumber = null,
        public ?string $company = null
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ('' === $this->displayName) {
            throw new InvalidArgumentException('A display name is required');
        }
    }
}
