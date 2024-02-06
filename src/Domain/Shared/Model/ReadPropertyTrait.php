<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:25
 */
declare(strict_types=1);

namespace App\Domain\Shared\Model;

use App\Domain\Error\LogicError;

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
