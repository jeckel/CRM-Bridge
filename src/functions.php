<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App;

use BackedEnum;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

if (! function_exists('App\slug')) {
    function slug(string $name): string
    {
        return strtolower((string) (new AsciiSlugger())->slug($name));
    }
}

if (! function_exists('App\new_uuid')) {
    function new_uuid(): UuidInterface
    {
        return Uuid::uuid4();
    }
}

if (! function_exists('App\enum_to_choices')) {
    /**
     * @param class-string<BackedEnum> $enumClass)
     * @return array<string, int|string>
     */
    function enum_to_choices(string $enumClass, string $i18nPrefix): array
    {
        return array_combine(
            array_map(fn(BackedEnum $service) => $i18nPrefix . '.' . $service->name, $enumClass::cases()),
            array_map(fn(BackedEnum $service) => $service->value, $enumClass::cases())
        );
    }
}
