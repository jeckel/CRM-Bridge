<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/01/2024 18:55
 */
declare(strict_types=1);

use Castor\Attribute\AsContext;
use Castor\Context;

use function Castor\fs;
use function Castor\io;
use function Castor\load_dot_env;

#[AsContext()]
function default_context(): Context
{
    $envFile = dirname(__DIR__). '/.env.local';
    return new Context(
        data: [],
        environment: fs()->exists($envFile) ? load_dot_env($envFile) : []
    );
}

function generate_env_file(string $envFile): void
{
    $env = [];
    if (fs()->exists($envFile)) {
        $env = load_dot_env($envFile);
    }
    $ngrokEdge = io()->ask('Ngrok Edge', $env['NGROK_EDGE'] ?? null);
    $ngrokEntryPoint = io()->ask('Ngrok entry point', $env['NGROK_ENTRYPOINT'] ?? null);

    file_put_contents(
        $envFile,
        str_replace(
            ['%NGROK_EDGE%', '%NGROK_ENTRYPOINT%'],
            [$ngrokEdge, $ngrokEntryPoint],
            file_get_contents(dirname(__DIR__) . '/.env.local.sample')
        )
    );
}
