<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

final readonly class ImapFolder
{
    public function __construct(
        public string $fullpath,
        public int $attributes,
        public string $delimiter,
        public string $shortpath,
    ) {}
}
