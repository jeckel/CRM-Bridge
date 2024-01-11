<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace docker;

use Castor\Attribute\AsTask;

use function Castor\run;

#[AsTask(name: 'build', description: 'Build docker images')]
function task_install(): void
{
    build_docker_images();
}

#[AsTask(name: 'up', description: 'Start docker container', aliases: ['up'])]
function task_up(): void
{
    compose_up();
}

#[AsTask(name: 'stop', description: 'Stop docker container', aliases: ['stop'])]
function task_stop(): void
{
    docker_compose(['stop']);
}

#[AsTask(name: 'bash', description: 'Down docker container', aliases: ['bash'])]
function task_bash(): void
{
    compose_bash('php-fpm', ['--user', 'hostUser']);
}

function build_docker_images(): void
{
    build_image(
        path: '.docker/php-fpm/',
        tag: 'crm-bridge/php-fpm:latest',
        buildArgs: ['UID' => posix_getuid(), 'GID' => posix_getgid()]
    );
    build_image(
        path: '.docker/worker/',
        tag: 'crm-bridge/worker:latest',
        buildArgs: ['UID' => posix_getuid(), 'GID' => posix_getgid()]
    );
}
