<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace docker;

use Castor\Attribute\AsTask;

use function Castor\load_dot_env;
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
        timeout: 0,
        environment: load_dot_env(dirname(__DIR__). '/.env.dev.local')
    );
}

#[AsTask(name: 'stop', description: 'Stop docker container', aliases: ['stop'])]
function task_stop(): void
{
    run(command: ['docker', 'compose','stop']);
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

    run(
        command: [
            'docker',
            'build',
            '-t', 'crm-bridge/worker:latest',
            '--build-arg', 'UID=' . posix_getuid(),
            '--build-arg', 'GID=' . posix_getgid(),
            '.docker/worker/'
        ],
        timeout: 0
    );
}
