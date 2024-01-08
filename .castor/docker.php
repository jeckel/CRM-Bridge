<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace docker;

use Castor\Attribute\AsTask;

use function Castor\run;

#[AsTask(name: 'up', description: 'Start docker container', aliases: ['up'])]
function task_up(): void
{
    run(
        command: [
            'docker',
            'compose',
            'up'
        ],
        timeout: 0
    );
}

function container_build(): void
{
    run(
        command: [
            'docker',
            'build',
            '-t', 'crm-bridge/php-fpm:latest',
            '--build-arg', 'UID=' . posix_getuid(),
            '--build-arg', 'GID=' . posix_getgid(),
            '.docker/php-fpm/'
        ],
        timeout: 0
    );
}
