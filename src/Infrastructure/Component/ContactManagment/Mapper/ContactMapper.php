<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Component\ContactManagment\Mapper;

use App\Domain\Component\ContactManagment\Entity\Contact as DomainContact;
use App\Domain\Component\ContactManagment\Entity\ContactActivity;
use App\Domain\Component\ContactManagment\Entity\ContactActivityCollection;
use App\Identity\ContactActivityId;
use App\Identity\ContactId;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\Entity\ContactActivity as DoctrineContactActivity;
use App\Infrastructure\Doctrine\Repository\CompanyRepository;
use App\Infrastructure\Doctrine\Repository\ContactActivityRepository;
use App\ValueObject\Email;
use Ramsey\Uuid\Uuid;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
readonly class ContactMapper
{
    public function __construct(
        private ContactActivityRepository $activityRepository,
        private CompanyRepository $companyRepository,
        private SluggerInterface $slugger
    ) {}

    public function mapToDomain(DoctrineContact $contact): DomainContact
    {
        return new DomainContact(
            id: ContactId::from((string) $contact->getId()),
            displayName: $contact->getDisplayName(),
            firstName: $contact->getFirstname(),
            lastName: $contact->getLastname(),
            email: $contact->getEmail() !== null ? new Email($contact->getEmail()) : null,
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
                    ->setId(Uuid::uuid4()->toString());
            }
        }

        $entity
            ->setId((string) $contact->id)
            ->setFirstname($contact->firstName)
            ->setLastname($contact->lastName)
            ->setDisplayName($contact->displayName)
            ->setEmail($contact->email !== null ? (string) $contact->email : null)
            ->setPhoneNumber($contact->phoneNumber)
            ->setEspoContactId($contact->espoContactId)
            ->setCompany($company)
            ->setVCardUri($contact->vCardUri)
            ->setVCardEtag($contact->vCardEtag)
            ->setVCardLastSyncAt($contact->vCardLastSyncAt);

        if ($contact->activities->hasChanged()) {
            foreach ($contact->activities as $activity) {
                $entity->addActivity(
                    ($this->activityRepository->find((string) $activity->id) ?? new DoctrineContactActivity())
                        ->setId((string) $activity->id)
                        ->setDate($activity->date)
                        ->setSubject($activity->subject)
                        ->setDescription($activity->description)
                );
            }
        }
        return $entity;
    }
}
