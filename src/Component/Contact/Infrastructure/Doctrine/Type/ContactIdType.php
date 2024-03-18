<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Infrastructure\Doctrine\Type;

use App\Component\Shared\Identity\ContactId;
use App\Infrastructure\Doctrine\Type\Identity\AbstractIdentityType;
use Override;

/**
 * @extends AbstractIdentityType<ContactId>
 */
class ContactIdType extends AbstractIdentityType
{
    public const string NAME = 'contact_id';

    #[Override]
    protected function getIdentityFqcn(): string
    {
        return ContactId::class;
    }
}
