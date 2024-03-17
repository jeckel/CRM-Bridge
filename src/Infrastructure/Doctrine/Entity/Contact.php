<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use App\Component\Shared\ValueObject\EmailType;
use App\Infrastructure\Doctrine\EntityModel\CardDavAccount;
use App\Infrastructure\Doctrine\EntityModel\CardDavAddressBook;
use App\Infrastructure\Doctrine\EntityModel\Company;
use App\Infrastructure\Doctrine\EntityModel\ContactEmail;
use App\Infrastructure\Doctrine\Exception\RelationNotFoundException;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use Stringable;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(name: 'contact_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface $id;

    #[ORM\Column(type: Types::STRING, length: 180, nullable: false)]
    private string $displayName = '';

    #[ORM\Column(type: Types::STRING, length: 180, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::STRING, length: 180, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
    private ?string $phoneNumber;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
    private ?string $espoContactId = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $vCardUri = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $vCardEtag = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
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
     * @var Collection<string, ContactEmail> $emailAddresses
     */
    #[ORM\OneToMany(
        mappedBy: 'contact',
        targetEntity: ContactEmail::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $emailAddresses;

    /**
     * @var Collection<int, ImapMessage> $mails
     */
    #[ORM\OneToMany(
        mappedBy: 'contact',
        targetEntity: ImapMessage::class,
        orphanRemoval: false
    )]
    private Collection $mails;

    #[ORM\ManyToOne(
        cascade: ['persist'],
        inversedBy: 'contacts'
    )]
    #[ORM\JoinColumn(
        name: 'company_id',
        referencedColumnName: 'company_id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?Company $company = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(
        name: 'card_dav_address_book_id',
        referencedColumnName: 'card_dav_address_book_id',
        nullable: true
    )]
    private ?CardDavAddressBook $addressBook = null;
    private ContactId $contactId;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->mails = new ArrayCollection();
        $this->emailAddresses = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIdentity(): ContactId
    {
        if (! isset($this->contactId)) {
            $this->contactId = ContactId::from($this->id->toString());
        }
        return $this->contactId;
    }

    public function setId(UuidInterface $id): self
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
     * @return Collection<int, ImapMessage>
     */
    public function getMails(): Collection
    {
        return $this->mails;
    }

    public function addMail(ImapMessage $mail): static
    {
        if (!$this->mails->contains($mail)) {
            $this->mails->add($mail);
            $mail->setContact($this);
        }

        return $this;
    }

    public function removeMail(ImapMessage $mail): static
    {
        if ($this->mails->removeElement($mail)) {
            // set the owning side to null (unless already changed)
            if ($mail->getContact() === $this) {
                $mail->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<string, ContactEmail>
     */
    public function getEmailAddresses(): Collection
    {
        return $this->emailAddresses;
    }

    public function addEmailAddress(Email $emailAddress, EmailType $type = EmailType::PRIMARY): static
    {
        $found = array_filter(
            $this->emailAddresses->toArray(),
            static fn(ContactEmail $contactEmail) => $contactEmail->address()->equals($emailAddress)
        );
        if (count($found) === 0) {
            $this->emailAddresses->add(ContactEmail::new($emailAddress, $this, $type));
        }
        return $this;
    }

    public function getPrimaryEmailAddress(): ?Email
    {
        $filtered = $this->emailAddresses->filter(
            fn(ContactEmail $emailAddress) => $emailAddress->isPrimary()
        );
        if (count($filtered) > 0) {
            return array_values($filtered->toArray())[0]->address();
        }
        return null;
    }
    //
    //    public function removeEmailAddress(ContactEmail $emailAddress): static
    //    {
    //        if ($this->emailAddresses->removeElement($emailAddress)) {
    //            // set the owning side to null (unless already changed)
    //            if ($emailAddress->getContact() === $this) {
    //                $emailAddress->setContact(null);
    //            }
    //        }
    //
    //        return $this;
    //    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): Contact
    {
        $this->company = $company;
        return $this;
    }

    public function getAddressBook(): ?CardDavAddressBook
    {
        return $this->addressBook;
    }

    public function getAddressBookOrFail(): CardDavAddressBook
    {
        if (null === $this->addressBook) {
            throw new RelationNotFoundException('Address book not set');
        }
        return $this->addressBook;
    }

    public function getCardDavAccountOrFail(): CardDavAccount
    {
        return $this->getAddressBookOrFail()->getCardDavAccount();
    }

    public function setAddressBook(?CardDavAddressBook $addressBook): Contact
    {
        $this->addressBook = $addressBook;
        return $this;
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
}
