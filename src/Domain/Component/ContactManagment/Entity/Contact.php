<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:09
 */
declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Entity;

use App\Domain\Component\ContactManagment\Error\VCardUriChangedError;
use App\Domain\Component\ContactManagment\Port\VCard;
use App\Domain\Error\LogicError;
use App\Identity\ContactId;
use App\Identity\MailId;
use DateTimeImmutable;

/**
 * @property-read string $displayName
 * @property-read ?string $firstName
 * @property-read ?string $lastName
 * @property-read ?string $email
 * @property-read ?string $phoneNumber
 * @property-read ?string $espoContactId
 * @property-read ContactActivityCollection $activities
 * @property-read ?string $vCardUri
 * @property-read ?string $vCardEtag
 * @property-read ?DateTimeImmutable $vCardLastSyncAt
 */
class Contact
{
    protected ContactActivityCollection $activities;

    /**
     * @suppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        public readonly ContactId $id,
        protected string $displayName,
        protected ?string $firstName = null,
        protected ?string $lastName = null,
        protected ?string $email = null,
        protected ?string $phoneNumber = null,
        protected ?string $espoContactId = null,
        ?ContactActivityCollection $activities = null,
        protected ?string $vCardUri = null,
        protected ?string $vCardEtag = null,
        protected ?DateTimeImmutable $vCardLastSyncAt = null
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

    public function updateFromVCard(VCard $vCard, DateTimeImmutable $vCardSyncAt): Contact
    {
        $this->displayName = $vCard->displayName();
        $this->firstName = $vCard->firstName();
        $this->lastName = $vCard->lastName();
        $this->email = $vCard->email();
        $this->phoneNumber = $vCard->phoneNumber();
        if (null !== $this->vCardUri && $this->vCardUri !== $vCard->vCardUri()) {
            throw new VCardUriChangedError('Can not change vCardUri');
        }
        $this->vCardUri = $vCard->vCardUri();
        $this->vCardEtag = $vCard->vCardEtag();
        $this->vCardLastSyncAt = $vCardSyncAt;
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

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            /** @phpstan-ignore-next-line  */
            return $this->$name;
        }
        throw new LogicError("Undefined property: {$name}");
    }

}
