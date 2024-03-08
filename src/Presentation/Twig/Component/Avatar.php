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

    public function getAvatar(): ?string
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
        return $this->retrieveGravatarUrl($this->email) ??
            $this->retrieveBimiUrl($this->email);
    }

    private function retrieveGravatarUrl(string $email): ?string
    {
        $url = "https://www.gravatar.com/avatar/" .
            md5(strtolower(trim($email))) .
            "?d=" . urlencode($this->default) .
            "&s=" . $this->size
        ;
        if ($this->is404($url)) {
            return null;
        }
        return $url;
    }

    private function retrieveBimiUrl(string $email): ?string
    {
        // @todo Extract only root domain, not sub-domain
        $domain = substr(strrchr($email, "@"), 1);

        // Perform DNS query for BIMI record
        $records = dns_get_record("default._bimi.$domain", DNS_TXT);

        // Check if BIMI record exists
        if (empty($records)) {
            return null;
        }

        // Extract URL from BIMI record
        foreach ($records as $record) {
            if (isset($record['entries'])) {
                foreach ($record['entries'] as $entry) {
                    if (strpos($entry, 'v=BIMI1') !== false) {
                        preg_match('/l=([^;\s]+)/', $entry, $matches);
                        if (isset($matches[1])) {
                            return $matches[1];
                        }
                    }
                }
            }
        }

        return null;
    }
}
