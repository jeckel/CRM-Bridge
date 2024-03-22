<?php

/**
 * @aut
 * hor Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 10:02
 */
declare(strict_types=1);

namespace App\Presentation\Common\Service\Avatar\Provider;

use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use App\Presentation\Common\Service\Avatar\AvatarContactDto;
use App\Presentation\Common\Service\Avatar\AvatarDtoInterface;
use Doctrine\ORM\EntityManagerInterface;
use Override;

readonly class ChainAvatarProvider implements AvatarProviderInterface
{
    public function __construct(
        private BimiProvider $bimiProvider,
        private GravatarProvider $gravatarProvider,
        private CardDavAvatarProvider $cardDavProvider,
        private EntityManagerInterface $entityManager
    ) {}

    #[Override]
    public function getAvatarFromEmail(Email $email, int $size = 40): ?AvatarDtoInterface
    {
        return
            $this->cardDavProvider->getAvatarFromEmail($email, $size) ??
            $this->gravatarProvider->getAvatarFromEmail($email, $size) ??
            $this->bimiProvider->getAvatarFromEmail($email, $size);
    }

    #[\Override]
    public function getAvatarFromContact(AvatarContactDto $contact, int $size = 40): ?AvatarDtoInterface
    {
        return
            $this->cardDavProvider->getAvatarFromContact($contact, $size) ??
            $this->gravatarProvider->getAvatarFromContact($contact, $size) ??
            $this->bimiProvider->getAvatarFromContact($contact, $size);
    }

    public function getAvatarFromContactId(ContactId $contactId, int $size = 40): ?AvatarDtoInterface
    {
        /** @var array{email: ?Email, vCardUri: string, accountId: CardDavAccountId} $result */
        $result = $this->entityManager->createQuery(
            'SELECT e.address as email, c.vCardUri, a.id as accountId
            FROM \App\Component\Contact\Domain\Entity\Contact c
            INNER JOIN \App\Component\CardDav\Domain\Entity\CardDavAddressBook ab WITH c.addressBook = ab.id
            INNER JOIN \App\Component\CardDav\Domain\Entity\CardDavAccount a WITH ab.account = a.id
            LEFT JOIN \App\Component\Contact\Domain\Entity\ContactEmail e WITH c.id = e.contact AND e.isPreferred = true
            WHERE c.id = :contact'
        )->setParameter('contact', $contactId)
            ->getSingleResult();
        return $this->getAvatarFromContact(new AvatarContactDto(...$result), $size);
    }
}
