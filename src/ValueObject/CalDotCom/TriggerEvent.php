<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\ValueObject\CalDotCom;

enum TriggerEvent: string
{
    case CREATED = 'BOOKING_CREATED';
    case CANCELLED = 'BOOKING_CANCELLED';
}
