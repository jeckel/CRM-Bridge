<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Message;

use App\ValueObject\Email;

readonly class CreateContact
{
    public function __construct(
        public string $displayName,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?Email $email = null,
        public ?string $phoneNumber = null
    ) {}
}
