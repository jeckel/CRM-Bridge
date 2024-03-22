<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 22/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Common\Service\Avatar;

use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\ValueObject\Email;

readonly class AvatarContactDto
{
    public function __construct(
        public ?Email $email,
        public string $vCardUri,
        public CardDavAccountId $accountId
    ) {}
}
