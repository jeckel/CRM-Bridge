<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:09
 */
declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Entity;

use App\Component\ContactManagment\Domain\Error\VCardUriChangedError;
use App\Component\Shared\DomainTrait\ReadPropertyTrait;
use App\Component\Shared\Event\ContactEmailAdded;
use App\Component\Shared\Event\DomainEventCollection;
use App\Component\Shared\Identity\CardDavAddressBookId;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\Identity\MailId;
use App\Component\Shared\ValueObject\Email;
use App\Component\Shared\ValueObject\EmailType;
use DateTimeImmutable;

/**
 * @property-read string $displayName
 * @property-read ?string $firstName
 * @property-read ?string $lastName
 * @property-read EmailAddressCollection $emailAddresses
 * @property-read ?string $phoneNumber
 * @property-read ?string $company
 * @property-read ?string $espoContactId
 * @property-read ContactActivityCollection $activities
 * @property-read ?string $vCardUri
 * @property-read ?string $vCardEtag
 * @property-read ?DateTimeImmutable $vCardLastSyncAt
 * @property-read ?CardDavAddressBookId $addressBookId
 */
class Contact
{
    use ReadPropertyTrait;

    protected ContactActivityCollection $activities;
    protected EmailAddressCollection $emailAddresses;

    /**
     * @suppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public readonly ContactId $id,
        protected string $displayName,
        protected ?string $firstName = null,
        protected ?string $lastName = null,
        ?EmailAddressCollection $emailAddresses = null,
        protected ?string $phoneNumber = null,
        protected ?string $company = null,
        protected ?string $espoContactId = null,
        ?ContactActivityCollection $activities = null,
        protected ?string $vCardUri = null,
        protected ?string $vCardEtag = null,
        protected ?DateTimeImmutable $vCardLastSyncAt = null,
        protected ?CardDavAddressBookId $addressBookId = null
    ) {
        $this->activities = $activities ?? new ContactActivityCollection();
        $this->emailAddresses = $emailAddresses ?? new EmailAddressCollection();
    }

    public static function create(
        string $displayName,
        ?string $firstName = null,
        ?string $lastName = null,
        ?Email $emailAddress = null,
        ?string $phoneNumber = null,
        ?string $company = null,
    ): self {
        $emailCollection = new EmailAddressCollection();
        if ($emailAddress !== null) {
            $emailCollection->add($emailAddress);
        }
        $contact = new self(
            id:  ContactId::new(),
            displayName: $displayName,
            firstName: $firstName,
            lastName: $lastName,
            emailAddresses: $emailCollection,
            phoneNumber: $phoneNumber,
            company: $company
        );
        if ($emailAddress !== null) {
            DomainEventCollection::getInstance()
                ->addDomainEvent(new ContactEmailAdded($contact->id, $emailAddress));
        }
        return $contact;
    }

    public function addEmailAddress(Email $email): self
    {
        $this->emailAddresses->add($email);
        DomainEventCollection::getInstance()
            ->addDomainEvent(new ContactEmailAdded($this->id, $email));
        return $this;
    }

    public function addAppointmentRequest(
        DateTimeImmutable $appointmentDate,
        DateTimeImmutable $requestDate,
        string $appointmentSubject
    ): self {
        $this->activities->add(ContactActivity::new(
            date: $requestDate,
            subject: 'Demande de rendez-vous',
            description: sprintf(
                "Date du rendez-vous: %s\nSujet: %s",
                $appointmentDate->format("d/m/Y"),
                $appointmentSubject
            )
        ));
        return $this;
    }

    public function addMail(MailId $mailId, DateTimeImmutable $sendAt): self
    {
        $this->activities->add(
            ContactActivity::new(
                date: $sendAt,
                subject: 'New email',
                description: sprintf(
                    "New mail %s (send: %s)",
                    $mailId->id(),
                    $sendAt->format("d/m/Y H:i:s")
                )
            )
        );
        return $this;
    }

    public function update(
        ?string $displayName = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?Email $emailAddress = null,
        ?string $phoneNumber = null,
        ?string $company = null,
    ): self {
        if (null !== $displayName) {
            $this->displayName = $displayName;
        }
        if (null !== $firstName) {
            $this->firstName = $firstName;
        }
        if (null !== $lastName) {
            $this->lastName = $lastName;
        }
        if (null !== $emailAddress) {
            $this->addEmailAddress($emailAddress);
        }
        if (null !== $phoneNumber) {
            $this->phoneNumber = $phoneNumber;
        }
        if (null !== $company) {
            $this->company = $company;
        }
        return $this;
    }

    public function linkVCard(
        string $vCardUri,
        string $vCardEtag,
        DateTimeImmutable $vCardLastSyncAt,
        CardDavAddressBookId $addressBookId
    ): self {
        if (null !== $this->vCardUri && $this->vCardUri !== $vCardUri) {
            throw new VCardUriChangedError('Can not change vCardUri');
        }
        $this->vCardUri = $vCardUri;
        $this->vCardEtag = $vCardEtag;
        $this->vCardLastSyncAt = $vCardLastSyncAt;
        $this->addressBookId = $addressBookId;
        return $this;
    }
}
