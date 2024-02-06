<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 14:21
 */
declare(strict_types=1);

namespace symfony;

use Castor\Attribute\AsTask;

use function project\run_symfony_console;

#[AsTask(name: 'cache:clear', description: 'Clear cache', aliases: ['c:c'])]
function task_database_migrate(): void
{
    run_symfony_console(['cache:clear']);
}
