<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 07:50
 */

namespace App\Presentation\Common\Service\Avatar\Provider;

use App\Infrastructure\Doctrine\Entity\Contact;
use App\Presentation\Common\Service\Avatar\AvatarDtoInterface;

interface AvatarProviderInterface
{
    public function getAvatarFromEmail(string $email, int $size = 40): ?AvatarDtoInterface;

    public function getAvatarFromContact(Contact $contact, int $size = 40): ?AvatarDtoInterface;
}
