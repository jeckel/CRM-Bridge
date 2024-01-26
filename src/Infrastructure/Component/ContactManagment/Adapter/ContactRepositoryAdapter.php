<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Component\ContactManagment\Adapter;

use App\Domain\Component\ContactManagment\Entity\Contact;
use App\Domain\Component\ContactManagment\Entity\ContactActivity;
use App\Domain\Component\ContactManagment\Entity\ContactActivityCollection;
use App\Domain\Component\ContactManagment\Port\ContactRepository;
use App\Identity\ContactActivityId;
use App\Identity\ContactId;
use App\Infrastructure\Doctrine\Entity\Contact as DoctrineContact;
use App\Infrastructure\Doctrine\Entity\ContactActivity as DoctrineContactActivity;
use App\Infrastructure\Doctrine\Repository\ContactActivityRepository;
use App\Infrastructure\Doctrine\Repository\ContactRepository as DoctrineContactRepository;

class ContactRepositoryAdapter implements ContactRepository
{
    public function __construct(
        private readonly DoctrineContactRepository $repository,
        private readonly ContactActivityRepository $activityRepository
    ) {}

    #[\Override]
    public function save(Contact $contact): void
    {
        $entity = $this->repository->find((string) $contact->id) ?? new DoctrineContact();
        $entity
            ->setId((string) $contact->id)
            ->setFirstname($contact->firstName)
            ->setLastname($contact->lastName)
            ->setDisplayName($contact->displayName)
            ->setEmail($contact->email)
            ->setPhoneNumber($contact->phoneNumber)
            ->setEspoContactId($contact->espoContactId);

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
        $this->repository->persist($entity);
    }

    #[\Override]
    public function findByEmail(string $email): ?Contact
    {
        $contact = $this->repository->findOneBy(['email' => $email]);
        if (null === $contact) {
            return null;
        }
        return new Contact(
            id: ContactId::from((string) $contact->getId()),
            firstName: $contact->getFirstname(),
            lastName: $contact->getLastname(),
            displayName: $contact->getDisplayName(),
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
            )
        );
    }
}
