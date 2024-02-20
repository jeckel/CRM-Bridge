<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\AccountId;
use App\Infrastructure\Doctrine\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Stringable;

use function App\slug;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'account_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    private string $slug;

    /**
     * @var Collection<int, User> $users
     */
    #[ORM\OneToMany(
        mappedBy: 'account',
        targetEntity: User::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $users;

    /**
     * @var Collection<string, CardDavConfig> $cardDavConfigs
     */
    #[ORM\OneToMany(
        mappedBy: 'account',
        targetEntity: CardDavConfig::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $cardDavConfigs;

    /**
     * @var Collection<string, ImapConfig> $imapConfigs
     */
    #[ORM\OneToMany(
        mappedBy: 'account',
        targetEntity: ImapConfig::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $imapConfigs;

    /**
     * @var Collection<int, AccountService> $services
     */
    #[ORM\OneToMany(
        mappedBy: 'account',
        targetEntity: AccountService::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $services;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->cardDavConfigs = new ArrayCollection();
        $this->imapConfigs = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getAccountId(): AccountId
    {
        return AccountId::from((string) $this->getId());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        if (!isset($this->slug)) {
            $this->setSlug(slug($name));
        }
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setAccount($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAccount() === $this) {
                $user->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<string, CardDavConfig>
     */
    public function getCardDavConfigs(): Collection
    {
        return $this->cardDavConfigs;
    }

    public function addCardDavConfig(CardDavConfig $cardDavConfig): static
    {
        if (!$this->cardDavConfigs->contains($cardDavConfig)) {
            $this->cardDavConfigs->add($cardDavConfig);
            $cardDavConfig->setAccount($this);
        }

        return $this;
    }

    public function removeCardDavConfig(CardDavConfig $cardDavConfig): static
    {
        if ($this->cardDavConfigs->removeElement($cardDavConfig)) {
            // set the owning side to null (unless already changed)
            if ($cardDavConfig->getAccount() === $this) {
                $cardDavConfig->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<string, ImapConfig>
     */
    public function getImapConfigs(): Collection
    {
        return $this->imapConfigs;
    }

    public function addImapConfig(ImapConfig $imapConfig): static
    {
        if (!$this->imapConfigs->contains($imapConfig)) {
            $this->imapConfigs->add($imapConfig);
            $imapConfig->setAccount($this);
        }

        return $this;
    }

    public function removeImapConfig(ImapConfig $imapConfig): static
    {
        if ($this->imapConfigs->removeElement($imapConfig)) {
            // set the owning side to null (unless already changed)
            if ($imapConfig->getAccount() === $this) {
                $imapConfig->setAccount(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, AccountService>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(AccountService $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setAccount($this);
        }

        return $this;
    }

    public function removeService(AccountService $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getAccount() === $this) {
                $service->setAccount(null);
            }
        }

        return $this;
    }
}
