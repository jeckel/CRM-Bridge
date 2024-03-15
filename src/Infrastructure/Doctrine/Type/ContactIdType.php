<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Component\Shared\Identity\ContactId;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ContactIdType extends AbstractIdentityType
{
    public const string NAME = 'contact_id';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?ContactId
    {
        if ($value instanceof ContactId) {
            return $value;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }
        return ContactId::from($value);
    }
}
