<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 03/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure;

use App\Infrastructure\Doctrine\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;

readonly class ConfigurationService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function has(string $key): bool
    {
        return $this->entityManager->getRepository(Configuration::class)->find($key) !== null;
    }

    public function get(string $key): mixed
    {
        $config = $this->entityManager->getRepository(Configuration::class)->find($key);
        return ($config !== null) ? $config->getValue() : null;
    }

    public function set(string $key, string $value): void
    {
        $config = ($this->entityManager->getRepository(Configuration::class)->find($key) ?? new Configuration())
            ->setProperty($key)
            ->setValue($value)
        ;
        if (null === $config->getValue()) {
            $config->setLabel($key);
        }
        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }
}
