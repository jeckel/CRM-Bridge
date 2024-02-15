<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:09
 */
declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Entity;

use App\Component\ContactManagment\Domain\Error\VCardUriChangedError;
use App\Component\ContactManagment\Domain\Port\VCard;
use App\Component\Shared\DomainTrait\ReadPropertyTrait;
use App\Component\Shared\Identity\AddressBookId;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\Identity\MailId;
use App\Component\Shared\ValueObject\Email;
use DateTimeImmutable;

/**
 * @property-read string $displayName
 * @property-read ?string $firstName
 * @property-read ?string $lastName
 * @property-read ?Email $email
 * @property-read ?string $phoneNumber
 * @property-read ?string $company
 * @property-read ?string $espoContactId
 * @property-read ContactActivityCollection $activities
 * @property-read ?string $vCardUri
 * @property-read ?string $vCardEtag
 * @property-read ?DateTimeImmutable $vCardLastSyncAt
 * @property-read ?AddressBookId $addressBookId
 */
class Contact
{
    use ReadPropertyTrait;

    protected ContactActivityCollection $activities;

    /**
     * @suppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public readonly ContactId $id,
        protected string $displayName,
        protected ?string $firstName = null,
        protected ?string $lastName = null,
        protected ?Email $email = null,
        protected ?string $phoneNumber = null,
        protected ?string $company = null,
        protected ?string $espoContactId = null,
        ?ContactActivityCollection $activities = null,
        protected ?string $vCardUri = null,
        protected ?string $vCardEtag = null,
        protected ?DateTimeImmutable $vCardLastSyncAt = null,
        protected ?AddressBookId $addressBookId = null
    ) {
        $this->activities = $activities ?? new ContactActivityCollection();
    }

    public static function new(string $displayName): self
    {
        return new self(
            id: ContactId::new(),
            displayName: $displayName
        );
    }

    public function updateFromVCard(VCard $vCard, DateTimeImmutable $vCardSyncAt, AddressBookId $addressBookId): Contact
    {
        $this->displayName = $vCard->displayName();
        $this->firstName = $vCard->firstName();
        $this->lastName = $vCard->lastName();
        $this->email = $vCard->email() !== null ? $vCard->email() : null;
        $this->phoneNumber = $vCard->phoneNumber();
        $this->company = $vCard->company();
        if (null !== $this->vCardUri && $this->vCardUri !== $vCard->vCardUri()) {
            throw new VCardUriChangedError('Can not change vCardUri');
        }
        $this->vCardUri = $vCard->vCardUri();
        $this->vCardEtag = $vCard->vCardEtag();
        $this->vCardLastSyncAt = $vCardSyncAt;
        $this->addressBookId = $addressBookId;
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
}
