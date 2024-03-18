<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Setup\Controller;

use App\Component\CardDav\Application\Command\UpdateAddressBooksActivation;
use App\Component\CardDav\Infrastructure\Doctrine\Repository\CardDavAccountRepository;
use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\CardDavAddressBookId;
use App\Presentation\Setup\Form\SyncedAddressBookFormType;
use App\Presentation\Setup\Query\ListCardDavAddressBooks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[Route("/setup/card_dav", name: "setup.card_dav.")]
#[IsGranted("ROLE_ADMIN")]
class SetupSyncedCardDavAction extends AbstractController
{

    #[Route(
        path: "/account/{accountId}/setup_address_books",
        name: "setup_address_books",
        methods: ['GET', 'POST']
    )]
    public function setupAddressBooks(
        string $accountId,
        Request $request,
        ListCardDavAddressBooks $listCardDavAddressBooks,
        MessageBusInterface $messageBus,
        CardDavAccountRepository $accountRepository
    ): Response {
        $addressBooks = $listCardDavAddressBooks(CardDavAccountId::from($accountId));
        $account = $accountRepository->getById($accountId);
        $form = $this->createForm(
            SyncedAddressBookFormType::class,
            null,
            [
                'hx-post' => $this->generateUrl(
                    'setup.card_dav.setup_address_books',
                    ['accountId' => $accountId]
                ),
                'addressBooks' => $addressBooks,
                'accountId' => $accountId,
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{accountId: string, syncedAddressBooks: string[]} $data */
            $data = $form->getData();
            $messageBus->dispatch(
                $this->createUpdateAddressBooksActivationFromFormData($data)
            );
            return $this->render('@modal/success.html.twig', [
                'message' => new TranslatableMessage(
                    'setup.flash_message.card_dav_account_added',
                    ['%account%' => $account->name()],
                    'messages'
                ),
                'refresh' => [
                    'route' => 'setup.card_dav.index',
                    'target' => '#cardDavList'
                ]
            ]);
        }
        return $this->render(
            '@modal/form.html.twig',
            [
                'title' => 'setup.card_dav.title.setup_address_books',
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @param array{accountId: string, syncedAddressBooks: string[]} $data
     * @return UpdateAddressBooksActivation
     */
    private function createUpdateAddressBooksActivationFromFormData(array $data): UpdateAddressBooksActivation
    {
        return new UpdateAddressBooksActivation(
            accountId: CardDavAccountId::from($data['accountId']),
            enabledAddressBookIds: array_map(
                static fn(string $addressBookId) => CardDavAddressBookId::from($addressBookId),
                $data['syncedAddressBooks']
            )
        );
    }
}
