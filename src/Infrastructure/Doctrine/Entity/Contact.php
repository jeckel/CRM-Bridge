<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Doctrine\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Stringable;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'contact_id', type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface|string $id;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    private string $displayName = '';

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 180, unique: true, nullable: true)]
    private ?string $email;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $phoneNumber;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $espoContactId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vCardUri = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vCardEtag = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $vCardLastSyncAt = null;

    /**
     * @var Collection<int, ContactActivity> $activities
     */
    #[ORM\OneToMany(
        mappedBy: 'contact',
        targetEntity: ContactActivity::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $activities;

    /**
     * @var Collection<int, Mail> $mails
     */
    #[ORM\OneToMany(
        mappedBy: 'contact',
        targetEntity: Mail::class,
        orphanRemoval: false
    )]
    private Collection $mails;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->mails = new ArrayCollection();
    }

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): Contact
    {
        $this->id = $id;
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): Contact
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): Contact
    {
        $this->firstname = $firstname;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Contact
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

    public function getEspoContactId(): ?string
    {
        return $this->espoContactId;
    }

    public function setEspoContactId(?string $espoContactId): Contact
    {
        $this->espoContactId = $espoContactId;
        return $this;
    }

    public function getVCardUri(): ?string
    {
        return $this->vCardUri;
    }

    public function setVCardUri(?string $vCardUri): Contact
    {
        $this->vCardUri = $vCardUri;
        return $this;
    }

    public function getVCardEtag(): ?string
    {
        return $this->vCardEtag;
    }

    public function setVCardEtag(?string $vCardEtag): Contact
    {
        $this->vCardEtag = $vCardEtag;
        return $this;
    }

    public function getVCardLastSyncAt(): ?\DateTimeImmutable
    {
        return $this->vCardLastSyncAt;
    }

    public function setVCardLastSyncAt(?\DateTimeImmutable $vCardLastSyncAt): Contact
    {
        $this->vCardLastSyncAt = $vCardLastSyncAt;
        return $this;
    }

    /**
     * @return Collection<int, ContactActivity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(ContactActivity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setContact($this);
        }

        return $this;
    }

    public function removeActivity(ContactActivity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getContact() === $this) {
                $activity->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mail>
     */
    public function getMails(): Collection
    {
        return $this->mails;
    }

    public function addMail(Mail $mail): static
    {
        if (!$this->mails->contains($mail)) {
            $this->mails->add($mail);
            $mail->setContact($this);
        }

        return $this;
    }

    public function removeMail(Mail $mail): static
    {
        if ($this->mails->removeElement($mail)) {
            // set the owning side to null (unless already changed)
            if ($mail->getContact() === $this) {
                $mail->setContact(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
