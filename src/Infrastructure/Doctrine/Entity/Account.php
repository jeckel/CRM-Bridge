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
use Override;
use Ramsey\Uuid\UuidInterface;
use Stringable;

use function App\slug;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account implements Stringable, SlugAwareInterface
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
        targetEntity: User::class,
        mappedBy: 'account',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $users;


    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    #[Override]
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    #[Override]
    public function getSlugSource(): string
    {
        return $this->name;
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

    public function __toString()
    {
        return $this->name;
    }
}
