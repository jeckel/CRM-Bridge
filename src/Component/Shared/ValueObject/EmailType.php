<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\ValueObject;

enum EmailType: string
{
    case PRIMARY = 'primary';
    case SECONDARY = 'secondary';
}
