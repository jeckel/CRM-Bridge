<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/02/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\DomainTrait;

use App\Component\Shared\Error\LogicError;

trait ReadPropertyTrait
{
    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            /** @phpstan-ignore-next-line  */
            return $this->$name;
        }
        throw new LogicError("Undefined property: {$name}");
    }
}
