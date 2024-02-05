<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

namespace App\Infrastructure\Configuration;

enum ConfigurationKey: string
{
    case CARDDAV_DEFAULT_ADDRESS_BOOK = 'carddav.default_address_book';
}
