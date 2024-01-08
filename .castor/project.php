<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace project;

use Castor\Attribute\AsTask;

use function Castor\io;

#[AsTask(name: 'install', description: 'Install the project', aliases: ['install'])]
function task_install(): void
{
    io()->writeln('<info>Hello world</info>');
}
