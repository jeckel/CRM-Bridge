<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Common\Service\Avatar\Provider;

use App\Infrastructure\CardDav\CardDavClientProvider;
use App\Infrastructure\Doctrine\Entity\Contact;
use App\Presentation\Common\Service\Avatar\AvatarDto;
use App\Presentation\Common\Service\Avatar\AvatarDtoInterface;
use Override;
use Throwable;

readonly class CardDavAvatarProvider implements AvatarProviderInterface
{
    public function __construct(private CardDavClientProvider $cardDavClientProvider) {}

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function getAvatarFromEmail(string $email, int $size = 40): ?AvatarDtoInterface
    {
        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function getAvatarFromContact(Contact $contact, int $size = 40): ?AvatarDtoInterface
    {
        $vCardUri = $contact->getVCardUri();
        if (null === $vCardUri) {
            return null;
        }

        try {
            $vCard = $this->cardDavClientProvider
                ->getClient($contact->getCardDavAccountOrFail())
                ->getVCard($vCardUri);
            if (! $vCard->hasPhoto()) {
                return null;
            }
            return new AvatarDto(
                content: $vCard->getPhotoContent(),
                mimeType: $vCard->getPhotoMimeType()
            );
        } catch (Throwable) {
            return null;
        }
    }
}
