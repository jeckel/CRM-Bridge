<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Domain\Entity;

use App\Component\Shared\Identity\ContactActivityId;
use DateTimeImmutable;

class ContactActivity
{
    private ContactActivityId $id;
    private Contact $contact;
    private DateTimeImmutable $date;
    private string $subject;
    private string $description;

    public static function new(
        string $subject,
        string $description,
        DateTimeImmutable $at,
        Contact $contact
    ): self {
        $activity = new self();
        $activity->id = ContactActivityId::new();
        $activity->contact = $contact;
        $activity->date = $at;
        $activity->subject = $subject;
        $activity->description = $description;
        return $activity;
    }
}
