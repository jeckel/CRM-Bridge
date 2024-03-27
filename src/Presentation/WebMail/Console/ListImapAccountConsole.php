<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 15:50
 */
declare(strict_types=1);

namespace App\Presentation\WebMail\Console;

use App\Component\Shared\Identity\ImapAccountId;
use App\Component\Shared\Identity\ImapMailboxId;
use App\Component\WebMail\Application\Command\SyncImapAccount;
use App\Component\WebMail\Application\Command\SyncMailbox;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:webmail:list-accounts', description: 'List accounts')]
class ListImapAccountConsole extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var array{id: ImapAccountId, name: string, uri: string, login: string}[] $result */
        $result = $this->entityManager->createQuery(
            'SELECT a.id, a.name, a.uri, a.login
            FROM \App\Component\WebMail\Domain\Entity\ImapAccount a'
        )->execute();

        foreach ($result as $account) {
            $io->title($account['name']);
            $io->definitionList(
                ['id' => $account['id']],
                ['name' => $account['name']],
                ['uri' => $account['uri']],
                ['login' => $account['login']],
            );

            $io->section('Mailboxes :');
            $this->listMailboxes($account['id'], $io);
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        while (true) {
            $response = $helper->ask(
                $input,
                $output,
                new ChoiceQuestion(
                    'Action :',
                    ['sync-account', 'sync-mailbox', 'exit'],
                    'exit'
                )
            );
            switch ($response) {
                case 'exit':
                    break 2;
                case 'sync-account':
                    $this->syncAccount($input, $output, $io);
                    break;
                case 'sync-mailbox':
                    $this->syncMailbox($input, $output, $io);
                    break;
            }
        }
        return Command::SUCCESS;
    }

    protected function listMailboxes(ImapAccountId $accountId, SymfonyStyle $io): void
    {
        /** @var array{id: ImapMailboxId, name: string, imapPath: string}[] $result */
        $result = $this->entityManager->createQuery('SELECT m.id, m.name, m.imapPath FROM \App\Component\WebMail\Domain\Entity\ImapMailbox m WHERE m.account = :accountId')
            ->setParameter('accountId', $accountId)
            ->execute();
        $table = [];
        foreach ($result as $mailbox) {
            $table[] = [
                $mailbox['id'],
                $mailbox['name'],
                $mailbox['imapPath']
            ];
        }
        $io->table(
            ['Account Id', 'Name', 'URI'],
            $table
        );
    }

    private function syncAccount(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        /** @var array{id: ImapAccountId, name: string}[] $result */
        $result = $this->entityManager->createQuery(
            'SELECT a.id, a.name
            FROM \App\Component\WebMail\Domain\Entity\ImapAccount a'
        )->execute();

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        /** @var string $response */
        $response = $helper->ask(
            $input,
            $output,
            new ChoiceQuestion(
                'Account :',
                array_combine(
                    array_column($result, 'id'),    // @phpstan-ignore-line
                    array_column($result, 'name')
                )
            )
        );
        $accountId = ImapAccountId::from($response);
        $this->messageBus->dispatch(new SyncImapAccount($accountId));
        $io->success(sprintf('Account %s synced', $response));
        $this->listMailboxes($accountId, $io);
    }

    private function syncMailbox(InputInterface $input, OutputInterface $output, SymfonyStyle $io): void
    {
        /** @var array{id: ImapAccountId, name: string}[] $result */
        $result = $this->entityManager->createQuery(
            'SELECT a.id, a.name
            FROM \App\Component\WebMail\Domain\Entity\ImapMailbox a'
        )->execute();

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        /** @var string $response */
        $response = $helper->ask(
            $input,
            $output,
            new ChoiceQuestion(
                'Select mailbox to synchronize:',
                array_combine(
                    array_column($result, 'id'),    // @phpstan-ignore-line
                    array_column($result, 'name')
                )
            )
        );
        $io->info("Start synchronizing mailbox {$response}");
        $this->messageBus->dispatch(new SyncMailbox(ImapMailboxId::from($response)));
        //        dd($response);
    }
}
