<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Async\Calendly;

enum CalendlyEventType: string
{
    case CREATED = 'invitee.created';
    case CANCELED = 'invitee.canceled';
    case NO_SHOW = 'invitee_no_show.created';
}
