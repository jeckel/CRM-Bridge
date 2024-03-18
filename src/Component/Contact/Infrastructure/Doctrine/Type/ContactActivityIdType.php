<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Infrastructure\Doctrine\Type;

use App\Component\Shared\Identity\ContactActivityId;
use App\Infrastructure\Doctrine\Type\Identity\AbstractIdentityType;
use Override;

/**
 * @extends AbstractIdentityType<ContactActivityId>
 */
class ContactActivityIdType extends AbstractIdentityType
{
    public const string NAME = 'contact_activity_id';

    #[Override]
    protected function getIdentityFqcn(): string
    {
        return ContactActivityId::class;
    }
}
