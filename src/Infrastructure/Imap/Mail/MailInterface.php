<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/02/2024
 */

namespace App\Infrastructure\Imap\Mail;

use DateTimeImmutable;

interface MailInterface
{
    public function subject(): string;

    public function fromName(): string;

    public function date(): DateTimeImmutable;
}
