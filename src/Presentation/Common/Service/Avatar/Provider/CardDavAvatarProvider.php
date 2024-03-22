<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Common\Service\Avatar\Provider;

use App\Component\CardDav\Infrastructure\CardDav\CardDavClientProvider;
use App\Component\CardDav\Infrastructure\Doctrine\Repository\CardDavAccountRepository;
use App\Component\Shared\ValueObject\Email;
use App\Presentation\Common\Service\Avatar\AvatarContactDto;
use App\Presentation\Common\Service\Avatar\AvatarDto;
use App\Presentation\Common\Service\Avatar\AvatarDtoInterface;
use Override;
use Throwable;

readonly class CardDavAvatarProvider implements AvatarProviderInterface
{
    public function __construct(
        private CardDavClientProvider $cardDavClientProvider,
        private CardDavAccountRepository $accountRepository
    ) {}

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function getAvatarFromEmail(Email $email, int $size = 40): ?AvatarDtoInterface
    {
        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function getAvatarFromContact(AvatarContactDto $contact, int $size = 40): ?AvatarDtoInterface
    {
        try {
            $vCard = $this->cardDavClientProvider
                ->getClient($this->accountRepository->getById($contact->accountId))
                ->getVCard($contact->vCardUri);
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
