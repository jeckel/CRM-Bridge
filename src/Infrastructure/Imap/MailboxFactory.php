<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use PhpImap\Mailbox;

class MailboxFactory
{
    public static function getMailbox(
        ConfigurationService $configurationService
    ): Mailbox {
        return new Mailbox(
            $configurationService->get(ConfigurationKey::IMAP_HOST) ?? '',
            $configurationService->get(ConfigurationKey::IMAP_LOGIN) ?? '',
            $configurationService->get(ConfigurationKey::IMAP_PASSWORD) ?? ''
        );
    }
}
