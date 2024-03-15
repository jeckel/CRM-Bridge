<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Component\Shared\Identity\CompanyId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class CompanyIdType extends AbstractIdentityType
{
    public const string NAME = 'company_id';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?CompanyId
    {
        if ($value instanceof CompanyId) {
            return $value;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }
        return CompanyId::from($value);
    }
}
