<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 14/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Infrastructure\CardDav\VCard;

readonly class ComposedName
{
    public function __construct(
        public string $familyName,
        public string $firstName,
        public string $middleName,
        public string $honorificPrefix,
        public string $honorificSuffix
    ) {}
}
