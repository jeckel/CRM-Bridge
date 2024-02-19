<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/02/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\ValueObject;

enum Service: string
{
    case CAL_DOT_COM = 'cal.com';

    public function toRole(): string
    {
        return 'ROLE_' . $this->name;
    }
}
