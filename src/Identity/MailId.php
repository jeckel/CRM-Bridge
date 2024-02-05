<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Identity;

use JeckelLab\Contract\Domain\Identity\Exception\IdentityException;
use JeckelLab\IdentityContract\AbstractStringIdentity;

final readonly class MailId extends AbstractStringIdentity
{
    protected static function generateNewIdentity(): int|string
    {
        throw new class () extends \LogicException implements IdentityException {
            public function __construct()
            {
                parent::__construct('New mail id generation is not allowed');
            }
        };
    }
}
