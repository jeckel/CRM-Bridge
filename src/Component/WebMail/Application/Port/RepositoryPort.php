<?php
/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:01
 */

namespace App\Component\WebMail\Application\Port;

use App\Component\WebMail\Domain\Entity\ImapAccount;

interface RepositoryPort
{
    public function persistAccount(ImapAccount $account): void;
}
