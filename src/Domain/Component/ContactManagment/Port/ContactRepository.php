<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 24/01/2024 20:08
 */
declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Port;

use App\Component\Shared\Identity\AccountId;
use App\Component\Shared\ValueObject\Email;
use App\Domain\Component\ContactManagment\Entity\Contact;

interface ContactRepository
{
    public function save(Contact $contact): void;

    public function findByEmail(EMail $email, AccountId $accountId): ?Contact;

    public function findByVCard(string $vCardUri, AccountId $accountId): ?Contact;
}
