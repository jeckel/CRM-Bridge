<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Domain\Entity;

use App\Component\CardDav\Domain\Entity\CardDavAccount;
use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\Contact\Domain\Event\ContactCreated;
use App\Component\Shared\Identity\ContactId;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JeckelLab\Contract\Domain\Entity\DomainEventAwareInterface;
use JeckelLab\Contract\Domain\Entity\DomainEventAwareTrait;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @phpstan-import-type vCardEmailDto from ContactEmail
 */
class Contact implements DomainEventAwareInterface
{
    use DomainEventAwareTrait;
    use ContactEmailCollectionTrait;

    private ContactId $id;
    private string $displayName;
    private string $vCardUri;
    private string $vCardEtag;
    private DateTimeImmutable $vCardLastSyncAt;  /** @phpstan-ignore-line  */
    private ?Company $company = null;
    public CardDavAddressBook $addressBook;

    /**
     * @var Collection<int, ContactActivity> $activities
     */
    private Collection $activities;

    /**
     * @var Collection<int, ImapMessage> $mails
     */
    private Collection $mails; /** @phpstan-ignore-line  */

    private function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->emailAddresses = new ArrayCollection();
        $this->mails = new ArrayCollection();
    }

    /**
     * @param vCardEmailDto[] $emails
     */
    public static function new(
        string $displayName,
        string $vCardUri,
        string $vCardEtag,
        DateTimeImmutable $vCardLastSyncAt,
        CardDavAddressBook $addressBook,
        ?Company $company,
        array $emails
    ): self {
        $contact = new self();
        $contact->id = ContactId::new();
        $contact->displayName = $displayName;
        $contact->vCardUri = $vCardUri;
        $contact->vCardEtag = $vCardEtag;
        $contact->vCardLastSyncAt = $vCardLastSyncAt;
        $contact->addressBook = $addressBook;
        $contact->company = $company;
        foreach ($emails as $email) {
            $contact->addEmail($email);
        }
        $contact->addActivity(
            subject: 'contact created',
            description: 'contact created',
            activityAt: $vCardLastSyncAt
        );
        $contact->addDomainEvent(new ContactCreated(
            contactId: $contact->id,
            occurredAt: $vCardLastSyncAt
        ));
        return $contact;
    }

    /**
     * @param vCardEmailDto[] $emails
     */
    public function update(
        string $displayName,
        string $vCardEtag,
        DateTimeImmutable $vCardLastSyncAt,
        ?Company $company,
        array $emails
    ): self {
        $updated = [];
        if ($displayName !== $this->displayName) {
            $this->displayName = $displayName;
            $updated[] = 'Display Name';
        }
        if ($vCardEtag !== $this->vCardEtag) {
            $this->vCardEtag = $vCardEtag;
            $updated[] = 'eTag';
        }
        if ($company !== $this->company) {
            $this->company = $company;
            $updated[] = 'Company';
        }
        foreach ($emails as $email) {
            if ($this->addEmail($email)) {
                $updated[] = 'Email';
            }
        }
        if (count($updated) > 0) {
            $this->vCardLastSyncAt = $vCardLastSyncAt;
            $this->addActivity(
                subject: 'contact updated',
                description: sprintf('Contact updated : %s', implode(', ', $updated)),
                activityAt: $vCardLastSyncAt
            );
        }
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

    public function id(): ContactId
    {
        return $this->id;
    }

    public function companyName(): ?string
    {
        return $this->company?->name();
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function cardDavAccount(): CardDavAccount
    {
        return $this->addressBook->account();
    }

    public function vCardUri(): string
    {
        return $this->vCardUri;
    }
}
