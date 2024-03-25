<?php

namespace App\Component\WebMail\Domain\Entity;

use App\Component\Shared\Identity\ContactId;
use Doctrine\Common\Collections\Collection;

readonly class ImapMailAuthor
{
    public ContactId $id;
    private Collection $mails;
}
