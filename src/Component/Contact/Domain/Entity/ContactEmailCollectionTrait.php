<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 22/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Domain\Entity;

use App\Component\Contact\Domain\Event\ContactEmailAdded;
use App\Component\Shared\ValueObject\Email;
use Doctrine\Common\Collections\Collection;

/**
 * @phpstan-import-type vCardEmailDto from ContactEmail
 */
trait ContactEmailCollectionTrait
{
    /**
     * @var Collection<string, ContactEmail> $emailAddresses
     */
    private Collection $emailAddresses;

    /**
     * @param vCardEmailDto $emailData
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function addEmail(array $emailData): bool
    {
        if ($this->emailAddresses->exists(
            static fn($key, ContactEmail $email) => $email->address()->equals($emailData['email'])
        )) {
            return false;
        }
        $preferredEmail = $this->extractPreferredContactEmail();
        if (null === $preferredEmail) {
            $emailData['pref'] = true;
        }
        if (null !== $preferredEmail && true === $emailData['pref']) {
            $preferredEmail->setPreferred(false);
        }

        $this->emailAddresses->add(ContactEmail::fromVCardDto($this, $emailData));
        $this->addDomainEvent(new ContactEmailAdded(
            contactId: $this->id,
            emailAddress: $emailData['email']
        ));
        return true;
    }

    protected function extractPreferredContactEmail(): ?ContactEmail
    {
        /** @var ContactEmail|false $email */
        $email = $this->emailAddresses->filter(static fn(ContactEmail $email) => $email->isPreferred())->first();
        if (false === $email) {
            return null;
        }
        return $email;
    }

    public function preferredEmail(): ?Email
    {
        return $this->extractPreferredContactEmail()?->address();
    }

    /**
     * @return array<ContactEmail>
     */
    public function secondaryEmails(): array
    {
        return
            $this->emailAddresses->filter(static fn(ContactEmail $email) => !$email->isPreferred())
                ->toArray();
    }
}
