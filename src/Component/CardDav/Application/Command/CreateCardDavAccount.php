<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\CardDav\Application\Command;

readonly class CreateCardDavAccount
{
    public function __construct(
        public string $name,
        public string $uri,
        public string $login,
        #[\SensitiveParameter]
        public string $password
    ) {}
}
