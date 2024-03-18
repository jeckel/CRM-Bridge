<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Domain\Entity;

use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\Shared\Identity\ContactId;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Contact
{
    private ContactId $id;
    private string $displayName;
    private string $vCardUri;
    private string $vCardEtag;
    private DateTimeImmutable $vCardLastSyncAt;
    private ?Company $company = null;
    private CardDavAddressBook $addressBook;

    /**
     * @var Collection<int, ContactActivity> $activities
     */
    private Collection $activities;

    /**
     * @var Collection<string, ContactEmail> $emailAddresses
     */
    private Collection $emailAddresses;

    /**
     * @var Collection<int, ImapMessage> $mails
     */
    private Collection $mails;

    private function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->emailAddresses = new ArrayCollection();
        $this->mails = new ArrayCollection();
    }

    public static function new(
        string $displayName,
        string $vCardUri,
        string $vCardEtag,
        DateTimeImmutable $vCardLastSyncAt,
        CardDavAddressBook $addressBook,
        ?Company $company = null
    ): self {
        $contact = new self();
        $contact->id = ContactId::new();
        $contact->displayName = $displayName;
        $contact->vCardUri = $vCardUri;
        $contact->vCardEtag = $vCardEtag;
        $contact->vCardLastSyncAt = $vCardLastSyncAt;
        $contact->addressBook = $addressBook;
        $contact->company = $company;
        $contact->addActivity(
            subject: 'contact created',
            description: 'contact created',
            activityAt: $vCardLastSyncAt
        );
        return $contact;
    }

    public function update(
        DateTimeImmutable $vCardLastSyncAt,
    ): self {
        $this->vCardLastSyncAt = $vCardLastSyncAt;
        $this->addActivity(
            subject: 'contact updated',
            description: 'contact updated',
            activityAt: $vCardLastSyncAt
        );
        return $this;
    }

    public function addActivity(
        string $subject,
        string $description,
        DateTimeImmutable $activityAt
    ): self {
        $this->activities->add(ContactActivity::new($subject, $description, $activityAt, $this));
        return $this;
    }
}
