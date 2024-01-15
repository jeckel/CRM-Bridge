<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace deploy;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\run;

#[AsTask(name: 'build-image')]
function task_build_image()
{
    run(
        command: [
            'docker',
//            '-D',
            'build',
            '-t', 'public.ecr.aws/a3l5d7g9/jeckel-lab/crm-bridge/php-fpm:latest',
            '-f', '.docker/prod/Dockerfile',
            context()->currentDirectory,
        ],
        timeout: 0
    );

    run(
        command: [
            'docker',
            'push',
            'public.ecr.aws/a3l5d7g9/jeckel-lab/crm-bridge/php-fpm:latest'
        ]
    );
}
