<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\ValueObject\Service;
use App\Infrastructure\Doctrine\Repository\ServiceConnectorRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
#[ORM\Entity(repositoryClass: ServiceConnectorRepository::class)]
class ServiceConnector implements Stringable, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'account_service_id', type: "integer", unique: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false, enumType: Service::class)]
    private Service $service;

    #[ORM\Column(length: 255, nullable: false)]
    private string $accessToken = 'undefined';

    #[ORM\Column(nullable: false)]
    private bool $enabled = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function setService(Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): ServiceConnector
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function __toString(): string
    {
        return $this->service->value;
    }

    #[\Override]
    public function getRoles(): array
    {
        return [
            $this->service->toRole(),
        ];
    }

    #[\Override]
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->accessToken;
    }

    public function isValid(): bool
    {
        return $this->enabled === true && $this->accessToken !== 'undefined';
    }
}
