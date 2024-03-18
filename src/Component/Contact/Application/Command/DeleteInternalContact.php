<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Application\Command;

readonly class DeleteInternalContact
{
    public function __construct(
        public string $vCardUri,
    ) {}
}
