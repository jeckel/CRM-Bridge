<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 07:50
 */

namespace App\Presentation\Service\Avatar;

interface AvatarProviderInterface
{
    public function getAvatar(string $email, int $size = 40): ?AvatarDto;
}
