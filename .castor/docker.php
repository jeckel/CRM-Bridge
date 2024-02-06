<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace docker;

use Castor\Attribute\AsTask;

use function Castor\get_cache;
use function Castor\io;
use function Castor\run;


#[AsTask(name: 'up', description: 'Start docker container', aliases: ['up'])]
function task_up(): void
{
    docker_compose_up();
}

#[AsTask(name: 'stop', description: 'Stop docker container', aliases: ['stop'])]
function task_stop(): void
{
    docker_compose(['stop']);
}

#[AsTask(name: 'bash', description: 'Down docker container', aliases: ['bash'])]
function task_bash(): void
{
    compose_bash(container: 'web', options: ['--user', 'hostUser']);
}

#[AsTask(name: 'prod', description: 'Build prod images')]
function build_prod_image(): void
{
    $cache = get_cache();

    $item = $cache->getItem('docker-registry');
    $registry = $item->isHit() ? $item->get() : null;
    $registry = io()->ask('Docker registry url', $registry);

    $item->set($registry);
    $cache->save($item);

    $tag = $registry . '/crm-bridge/web:latest';
    docker_build(
        path: '',
        tag: $tag,
        options: ['-f', '.docker/prod-web/Dockerfile']
    );
    run(
        command: [
            'docker',
            'push',
            $tag
        ],
        timeout: 0
    );

    $tag = $registry . '/crm-bridge/worker:latest';
    docker_build(
        path: '',
        tag: $tag,
        options: ['-f', '.docker/prod-worker/Dockerfile']
    );
    run(
        command: [
            'docker',
            'push',
            $tag
        ],
        timeout: 0
    );
}
