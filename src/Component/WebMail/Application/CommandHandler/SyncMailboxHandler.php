<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 16:30
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\CommandHandler;

use App\Component\WebMail\Application\Command\SyncMailbox;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncMailboxHandler
{
    public function __invoke(SyncMailbox $command): void
    {
        dd($command);
    }
}
