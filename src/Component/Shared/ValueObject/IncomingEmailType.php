<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/03/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\ValueObject;

enum IncomingEmailType: string
{
    case UNDEFINED = 'undefined';
    case DIRECT = 'direct';
    case NEWSLETTER = 'newsletter';
    case NOTIFICATION = 'notification';
    case SOCIAL_NETWORK = 'social_network';
    case MARKETING = 'marketing';
}
