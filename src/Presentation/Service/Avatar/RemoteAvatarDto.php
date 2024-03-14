<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 10:06
 */
declare(strict_types=1);

namespace App\Presentation\Service\Avatar;

use App\Presentation\Service\Avatar\Exception\AvatarReadException;

readonly class RemoteAvatarDto implements AvatarDtoInterface
{
    public function __construct(
        public string $url,
        public string $mimeType
    ) {}

    #[\Override]
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    #[\Override]
    public function getContent(): string
    {
        $content = file_get_contents($this->url);
        if (false === $content) {
            throw new AvatarReadException('Unable to read file: ' . $this->url);
        }
        return $content;
    }
}
