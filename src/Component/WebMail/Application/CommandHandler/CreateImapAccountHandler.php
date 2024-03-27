<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 10:58
 */
declare(strict_types=1);

namespace App\Component\WebMail\Application\CommandHandler;

use App\Component\WebMail\Application\Command\CreateImapAccount;
use App\Component\WebMail\Application\Port\RepositoryPort;
use App\Component\WebMail\Domain\Entity\ImapAccount;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateImapAccountHandler
{
    public function __construct(
        private RepositoryPort $repository,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function __invoke(CreateImapAccount $command): void
    {
        $account = ImapAccount::new(
            $command->name,
            $command->uri,
            $command->login,
            $command->password
        );
        $this->repository->persistAccount($account);

        foreach ($account->popEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
