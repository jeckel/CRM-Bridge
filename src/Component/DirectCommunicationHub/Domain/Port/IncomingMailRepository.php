<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:15
 */

namespace App\Component\DirectCommunicationHub\Domain\Port;

use App\Component\DirectCommunicationHub\Domain\Model\IncomingMail;
use App\Component\Shared\ValueObject\Email;

interface IncomingMailRepository
{
    public function save(IncomingMail $incomingMail): void;

    /**
     * @param Email $authorEmail
     * @return iterable<IncomingMail>
     */
    public function findByAuthorEmail(Email $authorEmail): iterable;
}
