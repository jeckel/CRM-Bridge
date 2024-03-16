<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type\Identity;

use App\Component\Shared\Identity\ContactId;
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