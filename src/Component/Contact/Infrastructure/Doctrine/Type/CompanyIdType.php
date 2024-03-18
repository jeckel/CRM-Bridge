<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Infrastructure\Doctrine\Type;

use App\Component\Shared\Identity\CompanyId;
use App\Infrastructure\Doctrine\Type\Identity\AbstractIdentityType;
use Override;

/**
 * @extends AbstractIdentityType<CompanyId>
 */
class CompanyIdType extends AbstractIdentityType
{
    public const string NAME = 'company_id';

    #[Override]
    protected function getIdentityFqcn(): string
    {
        return CompanyId::class;
    }
}
