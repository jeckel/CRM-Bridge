<?php

namespace App\Component\WebMail\Domain\Entity;

use App\Component\Shared\Identity\ImapAccountId;
use Doctrine\Common\Collections\Collection;

class ImapAccount
{
    public readonly ImapAccountId $id;
    private string $name;
    private string $uri;
    private string $login;
    private string $password;
    private Collection $mailboxes;
}
