<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 07:39
 */
declare(strict_types=1);

namespace App\Presentation\Common\Service\Avatar\Provider;

use App\Component\Shared\ValueObject\Email;
use App\Presentation\Common\Service\Avatar\AvatarContactDto;
use App\Presentation\Common\Service\Avatar\AvatarDtoInterface;
use App\Presentation\Common\Service\Avatar\RemoteAvatarDto;
use Override;
use Stringable;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

use function App\getImageMimeType;

readonly class GravatarProvider implements AvatarProviderInterface
{
    public function __construct(
        private CacheInterface $gravatarCache,
    ) {}

    #[Override]
    public function getAvatarFromEmail(Email $email, int $size = 40): ?AvatarDtoInterface
    {
        return $this->getFromEmail($email, $size);
    }

    #[Override]
    public function getAvatarFromContact(AvatarContactDto $contact, int $size = 40): ?AvatarDtoInterface
    {
        if (null === $contact->email) {
            return null;
        }
        return $this->getAvatarFromEmail($contact->email, $size);
    }

    public function getFromEmail(string|Stringable $email, int $size = 40): ?RemoteAvatarDto
    {
        $key = sprintf('%s-x-%d', str_replace('@', '-at-', (string) $email), $size);
        return $this->gravatarCache->get(
            $key,
            fn(ItemInterface $item): ?RemoteAvatarDto => $this->retrieveGravatarUrl($email, $size, $item)
        );
    }

    private function retrieveGravatarUrl(Stringable|string|Email $email, int $size, ItemInterface $item): ?RemoteAvatarDto
    {
        // Expire after 7 days
        $item->expiresAfter(3600 * 24 * 7);

        $url = "https://www.gravatar.com/avatar/" .
            md5(strtolower(trim((string) $email))) .
            "?d=404" .
            "&s=" . $size
        ;
        if ($this->is404($url)) {
            return null;
        }
        $mimeType = getImageMimeType($url);
        if (null === $mimeType) {
            return null;
        }
        return new RemoteAvatarDto(
            url: $url,
            mimeType: $mimeType
        );
    }

    private function is404(string $url): bool
    {
        $headers = get_headers($url);
        return !isset($headers[0]) || str_contains($headers[0], '404');
    }
}
