<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\ValueObject\Service;
use App\Infrastructure\Doctrine\Repository\AccountServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
#[ORM\Entity(repositoryClass: AccountServiceRepository::class)]
class AccountService implements Stringable, AccountAwareInterface, UserInterface
{
    use AccountAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'account_service_id', type: "integer", unique: true)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(
        name: 'account_id',
        referencedColumnName: 'account_id',
        nullable: false
    )]
    private ?Account $account = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $service = 'undefined';

    #[ORM\Column(length: 255, nullable: false)]
    private string $accessToken = 'undefined';

    #[ORM\Column(nullable: false)]
    private bool $enabled = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): AccountService
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
        return $this->service;
    }

    #[\Override]
    public function getRoles(): array
    {
        return [
            Service::from($this->service)->toRole(),
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
