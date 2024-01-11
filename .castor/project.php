<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace project;

use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;
use function docker\compose_run_or_exec;
use function docker\build_docker_images;

#[AsTask(name: 'install', description: 'Install the project', aliases: ['install'])]
function task_install(): void
{
//    io()->writeln('<info>Hello world</info>');
    build_docker_images();
}

#[AsTask(name: 'database:build', description: 'Build database')]
function task_build_database(): void
{
    run_symfony_console(['doctrine:schema:drop', '--force']);
    run_symfony_console(['doctrine:schema:create']);
    run_symfony_console(['doctrine:fixtures:load', '-n']);
}

#[AsTask(name: 'env:build', description: 'Build environment variables file')]
function task_env_build(): void
{
    generate_env_file(dirname(__DIR__). '/.env.local');
}

function run_symfony_console(array $command): void
{
    compose_run_or_exec('php-fpm', ['php', 'bin/console', ...$command], ['--user', 'hostUser']);
}
