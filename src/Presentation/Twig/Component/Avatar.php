<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/03/2024 11:39
 */
declare(strict_types=1);

namespace App\Presentation\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Avatar
{
    public ?string $contactId = null;
    public ?string $email = null;
    public ?string $size = null;

    public function getEncodedEmail(): ?string
    {
        return base64_encode($this->email ?? 'foo@bar.com');
    }
}
