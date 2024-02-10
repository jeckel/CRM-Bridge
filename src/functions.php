<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App;

use Symfony\Component\String\Slugger\AsciiSlugger;

if (! function_exists('App\slug')) {
    function slug(string $name): string
    {
        return strtolower((string) (new AsciiSlugger())->slug($name));
    }
}
