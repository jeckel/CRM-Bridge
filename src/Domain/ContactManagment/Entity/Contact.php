<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:09
 */
declare(strict_types=1);

namespace App\Domain\ContactManagment\Entity;

use App\Identity\ContactId;

class Contact
{
    public function __construct(
        public readonly ContactId $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $displayName,
        public readonly string $email,
        public readonly string $phoneNumber,
        public readonly string $espoContactId,
    ) { }
}
