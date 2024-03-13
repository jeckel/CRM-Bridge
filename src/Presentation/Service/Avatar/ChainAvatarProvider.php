<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 10:02
 */
declare(strict_types=1);

namespace App\Presentation\Service\Avatar;

use Override;

readonly class ChainAvatarProvider implements AvatarProviderInterface
{
    public function __construct(
        private BimiProvider $bimiProvider,
        private GravatarProvider $gravatarProvider
    ) {}

    #[Override]
    public function getAvatar(string $email, int $size = 40): ?AvatarDto
    {
        return $this->gravatarProvider->getAvatar($email, $size) ??
            $this->bimiProvider->getAvatar($email, $size);
    }
}
