<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\ContactActivityId;
use App\Infrastructure\Doctrine\Repository\ContactActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: ContactActivityRepository::class)]
class ContactActivity
{
    #[ORM\Id]
    #[ORM\Column(name: 'contact_activity_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'contact_id', nullable: false)]
    private ?Contact $contact;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $subject = '';

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $description = '';

    private ContactActivityId $activityId;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIdentity(): ContactActivityId
    {
        if (! isset($this->activityId)) {
            $this->activityId = ContactActivityId::from($this->id->toString());
        }
        return $this->activityId;
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
