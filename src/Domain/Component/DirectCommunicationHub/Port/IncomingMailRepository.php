<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:15
 */

namespace App\Domain\Component\DirectCommunicationHub\Port;

use App\Domain\Component\DirectCommunicationHub\Model\IncomingMail;
use App\ValueObject\Email;

interface IncomingMailRepository
{
    public function save(IncomingMail $incomingMail): void;

    /**
     * @param Email $authorEmail
     * @return iterable<IncomingMail>
     */
    public function findByAuthorEmail(Email $authorEmail): iterable;
}
