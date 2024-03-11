<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/03/2024 11:39
 */
declare(strict_types=1);

namespace App\Presentation\Twig\Component;

use App\Presentation\Service\Avatar\BimiProvider;
use App\Presentation\Service\Avatar\GravatarProvider;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Avatar
{
    public ?string $email;

    public function getEncodedEmail(): ?string
    {
        return base64_encode($this->email ?? 'foo@bar.com');
    }
}
