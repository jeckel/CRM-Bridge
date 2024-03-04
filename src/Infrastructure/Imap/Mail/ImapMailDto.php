<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 04/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap\Mail;

use LogicException;

/**
 * @property-read ?string $headersRaw
 * @property-read ?string $messageId
 * @property-read ?string $date
 * @property-read ?string $subject
 * @property-read ?string $fromName
 * @property-read ?string $fromAddress
 * @property-read ?string $toString
 */
readonly class ImapMailDto
{
    public function __construct(
        public ImapMailHeaderDto $headers,
        public string $textHtml,
        public string $textPlain
    ) {}

    public function __get(string $name): mixed
    {
        if (property_exists($this->headers, $name)) {
            return $this->headers->$name;
        }
        throw new LogicException("Undefined property: {$name}");
    }
}
