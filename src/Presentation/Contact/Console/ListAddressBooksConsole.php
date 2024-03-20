<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Console;

use App\Component\Shared\Identity\CardDavAccountId;
use App\Component\Shared\Identity\CardDavAddressBookId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @phpstan-type Record array{
 *     account_name: string,
 *     account_id: CardDavAccountId,
 *     address_book: string,
 *     address_book_id: CardDavAddressBookId,
 *     enabled: bool
 * }
 */
#[AsCommand(name: 'app:contact:list-address-books', description: 'List address books')]
class ListAddressBooksConsole extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /** @var array<Record> $result */
        $result = $this->entityManager->createQuery(
            'SELECT c.name as account_name, c.id as account_id, a.name as address_book, a.id as address_book_id, a.enabled
            FROM \App\Component\CardDav\Domain\Entity\CardDavAddressBook a
            LEFT JOIN \App\Component\CardDav\Domain\Entity\CardDavAccount c WITH a.account = c.id'
        )
            ->execute();
        $table = [];
        foreach ($result as $addressBook) {
            $table[] = [
                $addressBook['account_name'],
                $addressBook['account_id'],
                $addressBook['address_book'],
                $addressBook['address_book_id'],
                $addressBook['enabled'] ? 'Yes' : 'No',
            ];
        }
        $io->title('List of configured address books');
        $io->table(
            ['Account', 'Account Id', 'Address Book', 'Address Book Id', 'Enabled'],
            $table
        );
        return Command::SUCCESS;
    }
}
