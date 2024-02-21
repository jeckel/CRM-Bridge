<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace project;

use Castor\Attribute\AsTask;

use function Castor\run;
use function docker\compose_run_or_exec;

#[AsTask(name: 'database:build', description: 'Build database')]
function task_build_database(): void
{
    run_symfony_console(['doctrine:schema:drop', '--force']);
    run_symfony_console(['doctrine:schema:create']);
    run_symfony_console(['doctrine:fixtures:load', '-n']);
}

#[AsTask(name: 'database:migrate', description: 'Build database')]
function task_database_migrate(): void
{
    run_symfony_console(['doctrine:migration:migrate']);
}

#[AsTask(name: 'env:build', description: 'Build environment variables file')]
function task_env_build(): void
{
    generate_env_file(dirname(__DIR__). '/.env.local');
}

#[AsTask(name: 'update', description: 'Update the project', aliases: ['update'])]
function task_update(): void
{
    run(['git', 'pull']);
    run(['composer', 'update']);
}

#[AsTask(name: 'cache:clear', description: 'Clear cache', aliases: ['cc'])]
function task_cache_clear(): void
{
    run_symfony_console(['cache:clear']);
}

#[AsTask(name: 'cache:warmup', description: 'Warmup cache', aliases: ['cw'])]

function run_symfony_console(array $command): void
{
    compose_run_or_exec('web', ['php', 'bin/console', ...$command], ['--user', 'hostUser']);
}
