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
use function docker\container_build;

#[AsTask(name: 'install', description: 'Install the project', aliases: ['install'])]
function task_install(): void
{
//    io()->writeln('<info>Hello world</info>');
    container_build();
}

#[AsTask(name: 'database:build', description: 'Build database')]
function task_build_database(): void
{
    run(
        command: ['docker', 'compose', 'run', '--rm', 'worker', 'php', 'bin/console', 'doctrine:schema:create'],
    );
}
