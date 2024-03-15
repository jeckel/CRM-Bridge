<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Component\Shared\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Override;

class EmailType extends Type
{
    public const string NAME = 'email';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "varchar(255)";
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof Email) {
            return (string) $value;
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        if ($value instanceof Email) {
            return $value;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }
        return new Email($value);
    }

    #[Override]
    public function getName(): string
    {
        return self::NAME;
    }
}
