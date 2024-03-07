<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap\Exception;

use Throwable;

class MailboxNotFoundException extends \RuntimeException implements ImapException
{
    public function __construct(string $imapPath, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Mailbox "%s" not found', $imapPath),
            0,
            $previous
        );
    }
}
