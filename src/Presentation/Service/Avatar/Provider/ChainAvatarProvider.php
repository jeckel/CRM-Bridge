<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 10:02
 */
declare(strict_types=1);

namespace App\Presentation\Service\Avatar\Provider;

use App\Infrastructure\Doctrine\Entity\Contact;
use App\Presentation\Service\Avatar\AvatarDtoInterface;
use Override;

readonly class ChainAvatarProvider implements AvatarProviderInterface
{
    public function __construct(
        private BimiProvider $bimiProvider,
        private GravatarProvider $gravatarProvider,
        private CardDavAvatarProvider $cardDavProvider,
    ) {}

    #[Override]
    public function getAvatarFromEmail(string $email, int $size = 40): ?AvatarDtoInterface
    {
        return
            $this->cardDavProvider->getAvatarFromEmail($email, $size) ??
            $this->gravatarProvider->getAvatarFromEmail($email, $size) ??
            $this->bimiProvider->getAvatarFromEmail($email, $size);
    }

    #[\Override]
    public function getAvatarFromContact(Contact $contact, int $size = 40): ?AvatarDtoInterface
    {
        return
            $this->cardDavProvider->getAvatarFromContact($contact, $size) ??
            $this->gravatarProvider->getAvatarFromContact($contact, $size) ??
            $this->bimiProvider->getAvatarFromContact($contact, $size);
    }
}
