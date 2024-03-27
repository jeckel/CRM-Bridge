<?php

namespace App\Component\WebMail\Domain\Entity;

use App\Component\Shared\Identity\ImapAccountId;
use App\Component\WebMail\Application\Event\ImapAccountCreated;
use Doctrine\Common\Collections\Collection;
use JeckelLab\Contract\Domain\Entity\DomainEventAwareInterface;
use JeckelLab\Contract\Domain\Entity\DomainEventAwareTrait;

class ImapAccount implements DomainEventAwareInterface
{
    use DomainEventAwareTrait;

    private string $name;
    private string $uri;
    private string $login;
    private string $password;
    /** @var Collection<int, ImapMailbox>  */
    private Collection $mailboxes;  /** @phpstan-ignore-line  */

    private function __construct(public readonly ImapAccountId $id) {}

    public static function new(
        string $name,
        string $uri,
        string $login,
        #[\SensitiveParameter] string $password
    ): self {
        $account = new self(ImapAccountId::new());
        $account->name = $name;
        $account->uri = $uri;
        $account->login = $login;
        $account->password = $password;

        $account->addDomainEvent(new ImapAccountCreated($account->id));

        return $account;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function login(): string
    {
        return $this->login;
    }

    public function password(): string
    {
        return $this->password;
    }

    /**
     * @return Collection<int, ImapMailbox>
     */
    public function mailboxes(): Collection
    {
        return $this->mailboxes;
    }
}
