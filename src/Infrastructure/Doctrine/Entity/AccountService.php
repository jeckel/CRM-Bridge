<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\AccountServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass: AccountServiceRepository::class)]
class AccountService implements Stringable, AccountAwareInterface
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $authBearer = null;

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

    public function getAuthBearer(): ?string
    {
        return $this->authBearer;
    }

    public function setAuthBearer(?string $authBearer): static
    {
        $this->authBearer = $authBearer;

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
}
