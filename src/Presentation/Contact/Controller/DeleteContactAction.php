<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use App\Component\CardDav\Application\Command\DeleteCardDavContact;
use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\CardDavAddressBookId;
use App\Component\Shared\Identity\ContactId;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route(
    path: "/contact",
    name: "contact."
)]
class DeleteContactAction extends AbstractController
{
    #[Route(
        path: '/{contactId}/delete',
        name: 'delete',
        methods: ['GET']
    )]
    public function delete(
        string $contactId,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var array{vCardUri: string, displayName: string, addressBookId: CardDavAddressBookId, accountId: CardDavAccountId}|null $result */
        $result = $entityManager->createQuery(
            'SELECT c.vCardUri, c.displayName, ab.id AS addressBookId, cd.id as accountId
            FROM \App\Component\Contact\Domain\Entity\Contact c
            INNER JOIN c.addressBook ab
            INNER JOIN ab.account cd
            WHERE c.id = :contactId'
        )->setParameter('contactId', ContactId::from($contactId))
            ->getOneOrNullResult();

        if ($result === null) {
            throw new LogicException('Not found');
        }

        $messageBus->dispatch(new DeleteCardDavContact(
            cardDavAccountId: $result['accountId'],
            addressBookId: $result['addressBookId'],
            vCardUri: $result['vCardUri'],
        ));

        $this->addFlash('success', new TranslatableMessage(
            'contact.flash_message.contact_deleted',
            ['%contact%' => $result['displayName']]
        ));
        return $this->redirectToRoute('contact.index');
    }
}
