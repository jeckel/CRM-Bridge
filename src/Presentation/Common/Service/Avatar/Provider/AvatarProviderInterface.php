<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 07:50
 */

namespace App\Presentation\Common\Service\Avatar\Provider;

use App\Component\Shared\ValueObject\Email;
use App\Presentation\Common\Service\Avatar\AvatarContactDto;
use App\Presentation\Common\Service\Avatar\AvatarDtoInterface;

interface AvatarProviderInterface
{
    public function getAvatarFromEmail(Email $email, int $size = 40): ?AvatarDtoInterface;

    public function getAvatarFromContact(AvatarContactDto $contact, int $size = 40): ?AvatarDtoInterface;
}
