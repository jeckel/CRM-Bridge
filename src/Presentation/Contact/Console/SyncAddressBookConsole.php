<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Console;

use App\Component\CardDav\Application\Command\SyncCardDavAddressBook;
use App\Component\Shared\Identity\CardDavAddressBookId;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:contact:sync-address-book', description: 'Synchronize the address book')]
class SyncAddressBookConsole extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('address_book', InputArgument::REQUIRED, 'The address book Id to synchronize');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Address book synchronization');
        $id = $input->getArgument('address_book');
        if (! is_string($id)) {
            throw new InvalidArgumentException('The argument "address_book" must be a string');
        }
        $addressBookId = CardDavAddressBookId::from($id);

        $addressBookName = $this->getAddressBookName($addressBookId);
        $io->info(sprintf('Synchronizing the address book "%s"', $addressBookName));

        $this->messageBus->dispatch(
            new SyncCardDavAddressBook($addressBookId)
        );

        $io->success(sprintf('The address book "%s" has been synchronized', $addressBookName));
        return Command::SUCCESS;
    }

    /**
     * @param CardDavAddressBookId $addressBookId
     * @return string
     */
    protected function getAddressBookName(CardDavAddressBookId $addressBookId): string
    {
        /** @var string $addressBookName */
        $addressBookName = $this->entityManager->createQuery(
            'SELECT a.name
            FROM \App\Component\CardDav\Domain\Entity\CardDavAddressBook a
            WHERE a.id = :addressBookId'
        )->setParameter('addressBookId', $addressBookId)
            ->getSingleColumnResult()[0];
        return $addressBookName;
    }
}
