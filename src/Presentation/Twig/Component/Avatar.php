<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/03/2024 11:39
 */
declare(strict_types=1);

namespace App\Presentation\Twig\Component;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Avatar
{
    public string $displayName;
    public ?string $email = null;
    public string $default = '404';
    public int $size = 40;

    public function __construct(
        private readonly CacheInterface $avatarCache,
    ) {}

    public function getInitials(): string
    {
        $parts = explode(' ', $this->displayName);
        $initials = '';
        foreach($parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    public function getGravatar(): ?string
    {
        if (null === $this->email) {
            return null;
        }
        $key = sprintf('%s x %d', str_replace('@', '-at-', $this->email), $this->size);
        return $this->avatarCache->get(
            $key,
            fn(ItemInterface $item): ?string => $this->retrieveAvatarUrl($item)
        );
    }

    private function is404(string $url): bool
    {
        $headers = get_headers($url);
        return !isset($headers[0]) || str_contains($headers[0], '404');
    }

    private function retrieveAvatarUrl(ItemInterface $item): ?string
    {
        if (null === $this->email) {
            return null;
        }
        $item->expiresAfter(3600 * 24);
        $url = "https://www.gravatar.com/avatar/" .
            md5(strtolower(trim($this->email))) .
            "?d=" . urlencode($this->default) .
            "&s=" . $this->size
        ;
        if ($this->is404($url)) {
            return null;
        }
        return $url;
    }
}
