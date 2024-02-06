<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:14
 */
declare(strict_types=1);

namespace App\Domain\Component\DirectCommunicationHub\Model;

use App\Domain\Shared\Model\ReadPropertyTrait;
use App\Identity\ContactId;

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
