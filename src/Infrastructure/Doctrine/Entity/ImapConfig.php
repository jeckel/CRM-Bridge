<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\ImapConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Stringable;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
#[ORM\Entity(repositoryClass: ImapConfigRepository::class)]
class ImapConfig implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'imap_config_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name;

    #[ORM\Column(length: 255, nullable: false)]
    private string $uri;

    #[ORM\Column(length: 255, nullable: false)]
    private string $login;

    #[ORM\Column(length: 255, nullable: false)]
    private string $password;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: 'json')]
    private array $folders = [];

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): ImapConfig
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ImapConfig
    {
        $this->name = $name;
        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): ImapConfig
    {
        $this->uri = $uri;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): ImapConfig
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): ImapConfig
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getFolders(): array
    {
        return $this->folders;
    }

    /**
     * @param array<int, string> $folders
     * @return $this
     */
    public function setFolders(array $folders): ImapConfig
    {
        $this->folders = $folders;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
