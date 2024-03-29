<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\ValueObject\WebHookSource;
use App\Infrastructure\Doctrine\Repository\IncomingWebhookRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

#[ORM\Entity(repositoryClass: IncomingWebhookRepository::class)]
class IncomingWebhook
{
    #[ORM\Id]
    #[ORM\Column(name: 'incoming_webhook_id', type: "integer", unique: true)]
    #[ORM\GeneratedValue]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(length: 255, nullable: false, enumType: WebHookSource::class)]
    private WebHookSource $source;

    #[ORM\Column(length: 255, nullable: false)]
    private string $event = '';

    #[ORM\Column(type: Types::JSON, nullable: false)]
    private array $payload; /** @phpstan-ignore-line */

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(
        name: 'account_service_id',
        referencedColumnName: 'account_service_id',
        nullable: false
    )]
    private ?ServiceConnector $service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): WebHookSource
    {
        return $this->source;
    }

    public function setSource(WebHookSource $source): IncomingWebhook
    {
        $this->source = $source;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
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
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): static /** @phpstan-ignore-line */
    {
        $this->payload = $payload;

        return $this;
    }

    public function getService(): ?ServiceConnector
    {
        return $this->service;
    }

    public function setService(?ServiceConnector $service): IncomingWebhook
    {
        $this->service = $service;
        return $this;
    }
}
