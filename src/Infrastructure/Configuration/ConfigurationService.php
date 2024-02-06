<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Configuration;

use App\Infrastructure\Doctrine\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;

readonly class ConfigurationService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function has(ConfigurationKey ...$keys): bool
    {
        foreach ($keys as $key) {
            if ($this->entityManager->getRepository(Configuration::class)->find($key->value) === null) {
                return false;
            }
        }
        return true;
    }

    public function get(ConfigurationKey $key): ?string
    {
        $config = $this->entityManager->getRepository(Configuration::class)->find($key->value);
        return ($config !== null) ? $config->getValue() : null;
    }

    public function set(ConfigurationKey $key, string $value): void
    {
        $config = ($this->entityManager->getRepository(Configuration::class)->find($key->value) ?? new Configuration())
            ->setProperty($key->value)
            ->setValue($value)
        ;
        if (null === $config->getLabel()) {
            $config->setLabel($key->value);
        }
        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }
}
