<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/03/2024
 */

namespace App\Presentation\Service\Avatar;

interface AvatarDtoInterface
{
    public function getMimeType(): string;
    public function getContent(): string;
}
