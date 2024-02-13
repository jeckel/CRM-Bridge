<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:15
 */

namespace App\Domain\Component\DirectCommunicationHub\Port;

use App\Component\Shared\ValueObject\Email;
use App\Domain\Component\DirectCommunicationHub\Model\Author;

interface AuthorRepository
{
    public function findByEmail(Email $email): ?Author;
}
