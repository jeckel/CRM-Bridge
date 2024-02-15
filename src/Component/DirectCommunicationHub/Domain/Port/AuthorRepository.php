<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:15
 */

namespace App\Component\DirectCommunicationHub\Domain\Port;

use App\Component\DirectCommunicationHub\Domain\Model\Author;
use App\Component\Shared\ValueObject\Email;

interface AuthorRepository
{
    public function findByEmail(Email $email): ?Author;
}
