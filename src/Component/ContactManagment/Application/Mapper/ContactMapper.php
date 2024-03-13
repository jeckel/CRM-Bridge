<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Application\Mapper;

use App\Component\ContactManagment\Domain\Entity\Contact as DomainContact;
use App\Component\ContactManagment\Domain\Entity\ContactActivity;
use App\Component\ContactManagment\Domain\Entity\ContactActivityCollection;
use App\Component\ContactManagment\Domain\Entity\EmailAddressCollection;
use App\Component\Shared\Identity\ContactActivityId;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\CardDavAddressBook;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\Entity\ContactActivity as DoctrineContactActivity;
use App\Infrastructure\Doctrine\Entity\ContactEmailAddress;
use App\Infrastructure\Doctrine\Repository\CompanyRepository;
use App\Infrastructure\Doctrine\Repository\ContactActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\String\Slugger\SluggerInterface;

use function App\new_uuid;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ContactMapper
{
    public function __construct(
        //        private ContactActivityRepository $activityRepository,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger
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
            $slug = (string) $this->slugger->slug($contact->company);
            $company = $this->companyRepository->findOneBy(['slug' => $slug]);
            if (null === $company) {
                $company = (new Company())
                    ->setName($contact->company)
                    ->setSlug($slug)
                    ->setId(new_uuid());
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
            $addresses[$emailAddress->getEmailAddress()] = [
                'email' => new Email($emailAddress->getEmailAddress()),
                'type' => $emailAddress->getEmailType(),
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
        $repository = $this->entityManager->getRepository(ContactEmailAddress::class);
        foreach ($contact->emailAddresses as $strEmail => $emailAddressData) {
            $email = $repository->find($strEmail);
            $entity->addEmailAddress($email ?? (new ContactEmailAddress())->setEmailAddress($strEmail)->setEmailType($emailAddressData['type']));
        }
    }
}
