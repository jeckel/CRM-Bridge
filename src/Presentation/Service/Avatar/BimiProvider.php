<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 07:23
 */
declare(strict_types=1);

namespace App\Presentation\Service\Avatar;

use Override;
use Stringable;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

use function App\getImageMimeType;

readonly class BimiProvider implements AvatarProviderInterface
{
    public function __construct(
        private CacheInterface $bimiCache,
    ) {}

    #[Override]
    public function getAvatar(string $email, int $size = 40): ?AvatarDto
    {
        return $this->getFromEmail($email);
    }

    public function getFromEmail(string|Stringable $email): ?AvatarDto
    {
        // Extract domain from email
        $atPos = strrchr((string) $email, "@");
        if ($atPos === false) {
            // Not an email
            return null;
        }
        $domain = substr($atPos, 1);

        return $this->getFromDomainName($domain);
    }

    public function getFromDomainName(string $domain): ?AvatarDto
    {
        // Remove subdomains
        $parts = explode('.', $domain);
        $numParts = count($parts);
        if ($numParts > 2) {
            $domain = $parts[$numParts - 2] . '.' . $parts[$numParts - 1];
        }
        return $this->getFromRootDomainName($domain);
    }

    public function getFromRootDomainName(string $domain): ?AvatarDto
    {
        return $this->bimiCache->get(
            $domain,
            fn(ItemInterface $item): ?AvatarDto => $this->retrieveBimiUrl($domain, $item)
        );
    }

    private function retrieveBimiUrl(string $domain, ItemInterface $item): ?AvatarDto
    {
        // Expire after 7 days
        $item->expiresAfter(3600 * 24 * 7);

        // Perform DNS query for BIMI record
        $records = dns_get_record("default._bimi.$domain", DNS_TXT);

        // Check if BIMI record exists
        if (false === $records || count($records) === 0) {
            return null;
        }

        // Extract URL from BIMI record
        foreach ($records as $record) {
            if (isset($record['entries'])) {
                foreach ($record['entries'] as $entry) {
                    if (strpos($entry, 'v=BIMI1') === false) {
                        continue;
                    }
                    preg_match('/l=([^;\s]+)/', $entry, $matches);
                    if (! isset($matches[1])) {
                        continue;
                    }
                    return new AvatarDto(
                        url: $matches[1],
                        mimeType: 'image/svg+xml'
                    );
                }
            }
        }
        return null;
    }
}
