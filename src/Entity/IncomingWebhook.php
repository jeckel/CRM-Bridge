<?php

namespace App\Entity;

use App\Repository\IncomingWebhookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: IncomingWebhookRepository::class)]
class IncomingWebhook
{
    #[ORM\Id]
    #[ORM\Column(name: 'incoming_webhook__id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id; /** @phpstan-ignore-line */

    #[ORM\Column(length: 255)]
    private ?string $application = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $event = null;

    #[ORM\Column(type: Types::JSON)]
    private ?array $payload = null; /** @phpstan-ignore-line */

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function getApplication(): ?string
    {
        return $this->application;
    }

    public function setApplication(string $application): static
    {
        $this->application = $application;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): static
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): static /** @phpstan-ignore-line */
    {
        $this->payload = $payload;

        return $this;
    }
}
