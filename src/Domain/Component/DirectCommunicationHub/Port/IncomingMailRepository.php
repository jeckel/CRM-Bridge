<?php
/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 15:15
 */

namespace App\Domain\Component\DirectCommunicationHub\Port;

use App\Domain\Component\DirectCommunicationHub\Model\IncomingMail;

interface IncomingMailRepository
{
    public function save(IncomingMail $incomingMail): void;
}
