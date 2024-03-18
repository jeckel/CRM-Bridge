<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Command;

use App\Component\Shared\Identity\ContactId;

readonly class DeleteCardDavContact
{
    public function __construct(public ContactId $contactId) {}
}
