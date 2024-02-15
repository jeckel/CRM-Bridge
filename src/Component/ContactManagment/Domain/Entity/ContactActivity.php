<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Entity;

use App\Component\Shared\Identity\ContactActivityId;
use DateTimeImmutable;

readonly class ContactActivity
{
    public function __construct(
        public ContactActivityId $id,
        public DateTimeImmutable $date,
        public string $subject,
        public string $description
    ) {}

    public static function new(
        DateTimeImmutable $date,
        string $subject,
        string $description
    ): self {
        return new self(
            ContactActivityId::new(),
            $date,
            $subject,
            $description
        );
    }
}
