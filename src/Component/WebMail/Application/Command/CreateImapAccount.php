<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 10:50
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\Command;

readonly class CreateImapAccount
{
    public function __construct(
        public string $name,
        public string $uri,
        public string $login,
        #[\SensitiveParameter]
        public string $password
    ) {}
}
