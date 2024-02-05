<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\MailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MailRepository::class)]
class Mail
{
    #[ORM\Id]
    #[ORM\Column(name: 'mail_id', unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $messageId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $fromName = null;

    #[ORM\Column(length: 255)]
    private ?string $fromAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $toString = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $textPlain = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $textHtml = null;

    #[ORM\ManyToOne(inversedBy: 'mails')]
    #[ORM\JoinColumn(name: 'contact_id', referencedColumnName: 'contact_id', nullable: true)]
    private ?Contact $contact;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Mail
    {
        $this->id = $id;
        return $this;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function setMessageId(string $messageId): static
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): static
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(string $fromAddress): static
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getToString(): ?string
    {
        return $this->toString;
    }

    public function setToString(string $toString): static
    {
        $this->toString = $toString;

        return $this;
    }

    public function getTextPlain(): ?string
    {
        return $this->textPlain;
    }

    public function setTextPlain(string $textPlain): static
    {
        $this->textPlain = $textPlain;

        return $this;
    }

    public function getTextHtml(): ?string
    {
        return $this->textHtml;
    }

    public function setTextHtml(string $textHtml): static
    {
        $this->textHtml = $textHtml;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): Mail
    {
        $this->contact = $contact;
        return $this;
    }
}
