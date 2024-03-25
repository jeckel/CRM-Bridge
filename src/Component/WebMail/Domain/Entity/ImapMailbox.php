<?php

namespace App\Component\WebMail\Domain\Entity;

use App\Component\Shared\Identity\ImapMailboxId;
use DateTimeImmutable;

class ImapMailbox
{
    public readonly ImapMailboxId $id;
    private string $name;
    private string $imapPath;
    private string $slug;
    private ?int $lastSyncUid = null;
    private int $flags = 0;
    private int $messages = 0;
    private int $recent = 0;
    private int $unseen = 0;
    private int $uidNext = 0;
    private ?int $uidValidity = null;
    private ?DateTimeImmutable $lastSyncDate = null;
    private bool $enabled = true;

    private ImapAccount $account;

}
