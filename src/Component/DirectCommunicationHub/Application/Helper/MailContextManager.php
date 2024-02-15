<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Helper;

use App\Component\Shared\Error\LogicError;
use App\Component\Shared\Identity\ImapConfigId;
use App\Infrastructure\Doctrine\Entity\ImapConfig;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class MailContextManager
{
    private ?ImapConfigId $imapConfigId = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getImapConfigId(): ImapConfigId
    {
        if (null === $this->imapConfigId) {
            throw new LogicError('Imap config not defined yet');
        }
        return $this->imapConfigId;
    }

    public function setImapConfigId(?ImapConfigId $imapConfigId): void
    {
        $this->imapConfigId = $imapConfigId;
    }

    /**
     * @throws ORMException
     */
    public function getImapConfigReference(): ImapConfig
    {
        /** @var ImapConfig $imapConfigId */
        $imapConfigId = $this->entityManager->getReference(ImapConfig::class, $this->getImapConfigId()->id());
        return $imapConfigId;
    }
}
