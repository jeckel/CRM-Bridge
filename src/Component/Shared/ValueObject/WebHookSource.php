<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

namespace App\Component\Shared\ValueObject;

enum WebHookSource: string
{
    case CAL_DOT_COM = 'cal.com';
    case CALENDLY = 'calendly';
}
