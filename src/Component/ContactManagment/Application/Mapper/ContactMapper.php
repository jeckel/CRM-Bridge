<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Mapper;

use App\Component\CardDav\Domain\Entity\CardDavAddressBook;
use App\Component\ContactManagment\Domain\Entity\Contact as DomainContact;
use App\Component\ContactManagment\Domain\Entity\ContactActivity;
use App\Component\ContactManagment\Domain\Entity\ContactActivityCollection;
use App\Component\ContactManagment\Domain\Entity\EmailAddressCollection;
use App\Component\Shared\Identity\ContactActivityId;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use App\Component\Shared\ValueObject\EmailType;
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\EntityModel\Company;
use App\Infrastructure\Doctrine\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Ramsey\Uuid\Uuid;

use function App\slug;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ContactMapper
{
    public function __construct(
        //        private ContactActivityRepository $activityRepository,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function mapToDomain(DoctrineContact $contact): DomainContact
    {
        return new DomainContact(
            id: ContactId::from((string) $contact->getId()),
            displayName: $contact->getDisplayName(),
            firstName: $contact->getFirstname(),
            lastName: $contact->getLastname(),
            emailAddresses: $this->mapEmailAddressesToDomain($contact),
            phoneNumber: $contact->getPhoneNumber(),
            company: $contact->getCompany()?->getName(),
            espoContactId: $contact->getEspoContactId(),
            activities: new ContactActivityCollection(
                array_map(
                    static fn($activity) => new ContactActivity(
                        id: ContactActivityId::from((string) $activity->getId()),
                        date: $activity->getDate(),
                        subject: $activity->getSubject(),
                        description: $activity->getDescription()
                    ),
                    $contact->getActivities()->toArray(),
                )
            ),
            vCardUri: $contact->getVCardUri(),
            vCardEtag: $contact->getVCardEtag(),
            vCardLastSyncAt: $contact->getVCardLastSyncAt()
        );
    }

    /**
     * @throws ORMException
     */
    public function mapToDoctrine(DoctrineContact $entity, DomainContact $contact): DoctrineContact
    {
        $company = null;
        if ($contact->company !== null) {
            $company = $this->companyRepository->findBySlug(slug($contact->company));
            if (null === $company) {
                $company = Company::new($contact->company);
            }
        }
        $addressBook = null;
        if (null !== $contact->addressBookId) {
            $addressBook = $this->entityManager->getReference(CardDavAddressBook::class, $contact->addressBookId);
        }

        $entity
            ->setId(Uuid::fromString((string) $contact->id))
            ->setFirstname($contact->firstName)
            ->setLastname($contact->lastName)
            ->setDisplayName($contact->displayName)
            ->setPhoneNumber($contact->phoneNumber)
            ->setEspoContactId($contact->espoContactId)
            ->setCompany($company)
            ->setVCardUri($contact->vCardUri)
            ->setVCardEtag($contact->vCardEtag)
            ->setVCardLastSyncAt($contact->vCardLastSyncAt)
            ->setAddressBook($addressBook);

        $this->mapEmailAddressesToDoctrineEntity($contact, $entity);

        //        if ($contact->activities->hasChanged()) {
        //            foreach ($contact->activities as $activity) {
        //                $entity->addActivity(
        //                    ($this->activityRepository->find((string) $activity->id) ?? new DoctrineContactActivity())
        //                        ->setId((string) $activity->id)
        //                        ->setDate($activity->date)
        //                        ->setSubject($activity->subject)
        //                        ->setDescription($activity->description)
        //                );
        //            }
        //        }
        return $entity;
    }

    protected function mapEmailAddressesToDomain(DoctrineContact $contact): EmailAddressCollection
    {
        $addresses = [];
        foreach ($contact->getEmailAddresses() as $emailAddress) {
            $addresses[(string) $emailAddress->address()] = [
                'email' => $emailAddress->address(),
                'type' => $emailAddress->type(),
            ];
        }
        return new EmailAddressCollection($addresses);
    }

    /**
     * @param DomainContact $contact
     * @param DoctrineContact $entity
     * @return void
     */
    protected function mapEmailAddressesToDoctrineEntity(DomainContact $contact, DoctrineContact $entity): void
    {
        /**
         * @var array{email: Email, type: EmailType} $emailAddressData
         */
        foreach ($contact->emailAddresses as $emailAddressData) {
            $entity->addEmailAddress($emailAddressData['email'], $emailAddressData['type']);
        }
    }
}
