<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\CardDav;

use Exception;
use Sabre\VObject\Document;

class VCardHelper
{
    public static function hasPhoto(Document $vCard): bool
    {
        /** @phpstan-ignore-next-line  */
        return isset($vCard->PHOTO);
    }

    /**
     * @throws Exception
     */
    public static function getPhotoMimeType(Document $vCard): string
    {
        if (! self::hasPhoto($vCard)) {
            throw new InvalidArgumentException('No photo in vCard');
        }
        /** @phpstan-ignore-next-line  */
        return 'image/' . $vCard->PHOTO->parameters()['TYPE']->getValue();
    }

    /**
     * @throws Exception
     */
    public static function getPhotoContent(Document $vCard): string
    {
        if (! self::hasPhoto($vCard)) {
            throw new InvalidArgumentException('No photo in vCard');
        }
        /** @phpstan-ignore-next-line  */
        return $vCard->PHOTO->getValue();
    }
}
