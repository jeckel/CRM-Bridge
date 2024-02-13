<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:13
 */
declare(strict_types=1);

namespace App\Domain\Component\DirectCommunicationHub\Dto;

use App\Component\Shared\Identity\MailId;
use App\Component\Shared\ValueObject\Email;
use DateTimeImmutable;

final readonly class IncomingMailDto
{
    public function __construct(
        public MailId $id,
        public string $messageId,
        public DateTimeImmutable $date,
        public string $subject,
        public string $fromName,
        public EMail $fromAddress,
        public string $toString,
        public ?string $textPlain = null,
        public ?string $textHtml = null
    ) {}
}
