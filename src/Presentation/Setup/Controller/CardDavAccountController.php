<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/03/2024 11:54
 */
declare(strict_types=1);

namespace App\Presentation\Setup\Controller;

use App\Infrastructure\CardDav\CardDavClientProvider;
use App\Infrastructure\Doctrine\EntityModel\CardDavAccount;
use App\Infrastructure\Doctrine\EntityModel\CardDavAddressBook;
use App\Infrastructure\Doctrine\Repository\CardDavAccountRepository;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use App\Presentation\Setup\Form\CardDavAccountDto;
use App\Presentation\Setup\Form\CardDavAccountFormType;
use App\Presentation\Setup\Form\DefaultAddressBookFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[Route("/setup/card_dav", name: "setup.card_dav.")]
#[IsGranted("ROLE_ADMIN")]
class CardDavAccountController extends AbstractController
{
    public function __construct(
        private readonly CardDavClientProvider $cardDavClientProvider,
        private readonly CardDavAddressBookRepository $addressBookRepository,
        private readonly CardDavAccountRepository $cardDavConfigRepository
    ) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render(
            '@setup/card_dav/index_embed.html.twig',
            [
                'card_dav_accounts' => $this->cardDavConfigRepository->findAll()
            ]
        );
    }

    #[Route(
        path: "/create_account",
        name: "create_account",
        methods: ['GET', 'POST']
    )]
    public function addAccount(Request $request): Response
    {
        $form = $this->createForm(
            CardDavAccountFormType::class,
            new CardDavAccountDto(),
            [
                'hx-post' => $this->generateUrl('setup.card_dav.create_account'),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // @todo: Build Account from form resilt DTO
//            /** @var CardDavAccountDto $account */
//            $accountDto = $form->getData();
//            $account = CardDavAccount::fromFormDto($accountDto);
//            $this->cardDavConfigRepository->persist($account);
//            $this->fetchAddressBookFromAccount($account);
            return $this->redirect($this->generateUrl(
                'setup.card_dav.setup_address_books',
//                ['accountId' => (string) $account->getId()]
            ));
        }
        return $this->render(
            '@modal/form.html.twig',
            [
                'title' => 'setup.card_dav.title.create_account',
                'form' => $form->createView()
            ]
        );
    }

    #[Route(
        path: "/account/{accountId}/setup_address_books",
        name: "setup_address_books",
        methods: ['GET', 'POST']
    )]
    public function setupAddressBooks(string $accountId, Request $request): Response
    {
        $account = $this->cardDavConfigRepository->getById($accountId);
        $form = $this->createForm(
            DefaultAddressBookFormType::class,
            null,
            [
                'hx-post' => $this->generateUrl(
                    'setup.card_dav.setup_address_books',
                    ['accountId' => $accountId]
                ),
                'addressBooks' => $this->extractAddressBooksChoices($account)
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{syncedAddressBooks: string[], defaultAddressBook: string} $data */
            $data = $form->getData();
            // @todo Review how to handle default address book
            $this->updateAddressBooks($account, $data['syncedAddressBooks']);
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
     * @throws Exception
     */
    private function fetchAddressBookFromAccount(CardDavAccount $account): void
    {
        foreach($this->cardDavClientProvider->getClient($account)->discoverAddressBooks() as $addressBook) {
            if (null !== $this->addressBookRepository->findOneBy([
                    'uri' => $addressBook->getUri(),
                    'account' => $account,
                ])) {
                continue;
            }
            $entity = CardDavAddressBook::new(
                $addressBook->getName(),
                $addressBook->getUri(),
                $account
            );
            $this->addressBookRepository->persist($entity);
        }
    }

    /**
     * @param CardDavAccount $account
     * @return array<string, string>
     * @throws Exception
     */
    private function extractAddressBooksChoices(CardDavAccount $account): array
    {
        $addressBookEntities = $this->addressBookRepository->findByAccount($account);
        if (count($addressBookEntities) === 0) {
            $this->fetchAddressBookFromAccount($account);
            $addressBookEntities = $this->addressBookRepository->findByAccount($account);
        }
        $addressBooks = array_map(
            static fn(CardDavAddressBook $addressBook): string => $addressBook->getName(),
            $addressBookEntities
        );
        return array_combine($addressBooks, $addressBooks);
    }

    /**
     * @param CardDavAccount $account
     * @param string[] $syncedAddressBooks
     * @return void
     */
    private function updateAddressBooks(CardDavAccount $account, array $syncedAddressBooks): void
    {
        $addressBookEntities = $this->addressBookRepository->findByAccount($account);
        foreach ($addressBookEntities as $addressBook) {
            $addressBook->setEnabled(in_array($addressBook->getName(), $syncedAddressBooks, true));
            $this->addressBookRepository->persist($addressBook);
        }
    }
}
