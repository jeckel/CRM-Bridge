<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:04
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\Event;

use App\Component\Shared\Identity\ImapAccountId;
use JeckelLab\Contract\Domain\Event\Event;

readonly class ImapAccountCreated implements Event
{
    public function __construct(
        public ImapAccountId $id,
    ) {}
}
