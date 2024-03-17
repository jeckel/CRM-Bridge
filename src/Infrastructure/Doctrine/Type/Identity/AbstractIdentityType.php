<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type\Identity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use JeckelLab\IdentityContract\AbstractUuidIdentity;
use Override;

/**
 * @template T of AbstractUuidIdentity
 */
abstract class AbstractIdentityType extends Type
{
    public const string NAME = 'identity';

    /**
     * @return class-string<T>
     */
    abstract protected function getIdentityFqcn(): string;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_object($value) && is_a($value, $this->getIdentityFqcn())) {
            return (string) $value;
        }
        if (is_string($value)) {
            return $value;
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @phpstan-return AbstractUuidIdentity|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?AbstractUuidIdentity
    {
        if (is_object($value) && is_a($value, $this->getIdentityFqcn())) {
            return $value;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }
        return $this->getIdentityFqcn()::from($value);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return "varchar(36)";
    }

    #[Override]
    public function getName(): string
    {
        return static::NAME;
    }
}
