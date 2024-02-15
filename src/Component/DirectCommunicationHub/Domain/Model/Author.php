<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:14
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Domain\Model;

use App\Component\Shared\DomainTrait\ReadPropertyTrait;
use App\Component\Shared\Identity\ContactId;

/**
 * @property-read ContactId $id
 */
class Author
{
    use ReadPropertyTrait;

    public function __construct(
        protected ContactId $id
    ) {}
}
