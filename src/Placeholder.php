<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 2024-01-08
 */

declare(strict_types=1);

namespace Jeckel\CrmBridge;

final class Placeholder
{
    private string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function echo(string $value): string
    {
        return $this->prefix.$value;
    }
}
