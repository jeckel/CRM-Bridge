<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use JeckelLab\IdentityContract\AbstractUuidIdentity;

abstract class AbstractIdentityType extends Type
{
    public const string NAME = 'identity';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof AbstractUuidIdentity) {
            return (string) $value;
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "varchar(36)";
    }

    #[\Override]
    public function getName(): string
    {
        return self::NAME;
    }
}
