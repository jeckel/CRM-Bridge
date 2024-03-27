<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

declare(strict_types=1);

namespace App\Component\WebMail\Application\Dto;

final readonly class MailboxStatusDto
{
    public function __construct(
        public string $imapPath,
        public int $minUid = 0,
        public int $flags = 0,
        public int $messages = 0,
        public int $recent = 0,
        public int $unseen = 0,
        public int $uidnext = 0,
        public int $uidvalidity = 0
    ) {}
}
