<?php

use Castor\Attribute\AsContext;
use Castor\Context;

use function Castor\import;

import('vendor/jeckel-lab/castor-helper/src');
import('.castor');

#[AsContext(name: 'default_context', default: true)]
function create_default_context(): Context
{
    return new Context(
        data: array_merge(
            [
                'docker-fingerprint-directories' => [
                    '.docker'
                ],
                'docker-fingerprint-files' => [
                    'docker-compose.yml'
                ]
            ],
        )
    );
}
