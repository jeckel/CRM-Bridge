<?php

namespace App\Component\Security\Domain\Entity;

use App\Component\Shared\Identity\UserId;
use App\Component\Shared\ValueObject\Email;
use Override;
use Stringable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, Stringable
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private UserId $id,         /** @phpstan-ignore-line  */
        private string $username,
        private Email $email,       /** @phpstan-ignore-line  */
        private ?string $firstname = null,
        private ?string $lastname = null,
        private array $roles = ['ROLE_USER'],
        private ?string $password = null,
    ) {}

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     * @return string[]
     */
    #[Override]
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    #[Override]
    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    /**
     * @see UserInterface
     */
    #[Override]
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    #[Override]
    public function __toString(): string
    {
        if ($this->firstname !== '' || $this->lastname !== '') {
            return trim(sprintf('%s %s', $this->firstname, $this->lastname));
        }
        return $this->username;
    }
}
