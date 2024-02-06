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
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\Entity\ContactActivity as DoctrineContactActivity;
use App\Infrastructure\Doctrine\Repository\ContactActivityRepository;

readonly class ContactMapper
{
    public function __construct(
        private ContactActivityRepository $activityRepository,
    ) {}

    public function mapToDomain(DoctrineContact $contact): DomainContact
    {
        return new DomainContact(
            id: ContactId::from((string) $contact->getId()),
            displayName: $contact->getDisplayName(),
            firstName: $contact->getFirstname(),
            lastName: $contact->getLastname(),
            email: $contact->getEmail(),
            phoneNumber: $contact->getPhoneNumber(),
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
        $entity
            ->setId((string) $contact->id)
            ->setFirstname($contact->firstName)
            ->setLastname($contact->lastName)
            ->setDisplayName($contact->displayName)
            ->setEmail($contact->email)
            ->setPhoneNumber($contact->phoneNumber)
            ->setEspoContactId($contact->espoContactId)
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
