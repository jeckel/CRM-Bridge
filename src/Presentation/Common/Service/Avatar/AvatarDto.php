<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Common\Service\Avatar;

use Override;

readonly class AvatarDto implements AvatarDtoInterface
{
    public function __construct(public string $content, public string $mimeType) {}

    #[Override]
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    #[Override]
    public function getContent(): string
    {
        return $this->content;
    }
}
