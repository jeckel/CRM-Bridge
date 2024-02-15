<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:08
 */
declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Port;

use App\Component\ContactManagment\Domain\Entity\Contact;
use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\ValueObject\Email;

interface ContactRepository
{
    public function save(Contact $contact): void;

    public function findByEmail(EMail $email): ?Contact;

    public function findByVCard(string $vCardUri): ?Contact;
}
