<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\ValueObject\EmailType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ContactEmailAddress
{
    #[ORM\Id]
    #[ORM\Column(name: 'email_address', type: Types::STRING, unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private string $emailAddress;

    #[ORM\ManyToOne(
        inversedBy: 'emailAddresses'
    )]
    #[ORM\JoinColumn(
        name: 'contact_id',
        referencedColumnName: 'contact_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private ?Contact $contact = null;

    #[ORM\Column(enumType: EmailType::class)]
    private EmailType $emailType;

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    public function getEmailType(): EmailType
    {
        return $this->emailType;
    }

    public function setEmailType(EmailType $emailType): self
    {
        $this->emailType = $emailType;
        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->emailType === EmailType::PRIMARY;
    }
}
