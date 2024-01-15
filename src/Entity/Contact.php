<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\Column(name: 'contact_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, nullable: false)]
    private string $name = '';

    #[ORM\Column(length: 180)]
    private ?string $lastname = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column(length: 30)]
    private ?string $phoneNumber;

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): Contact
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Contact
    {
        $this->name = $name;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): Contact
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Contact
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): Contact
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}
