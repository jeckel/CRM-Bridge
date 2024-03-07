<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/02/2024
 */

declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Helper;

use App\Component\Shared\Error\LogicError;
use App\Component\Shared\Identity\ImapAccountId;
use App\Infrastructure\Doctrine\Entity\ImapAccount;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class MailContextManager
{
    private ?ImapAccountId $imapConfigId = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getImapConfigId(): ImapAccountId
    {
        if (null === $this->imapConfigId) {
            throw new LogicError('Imap config not defined yet');
        }
        return $this->imapConfigId;
    }

    public function setImapConfigId(?ImapAccountId $imapConfigId): void
    {
        $this->imapConfigId = $imapConfigId;
    }

    public function hasImapConfig(): bool
    {
        return null !== $this->imapConfigId;
    }

    /**
     * @throws ORMException
     */
    public function getImapConfigReference(): ImapAccount
    {
        /** @var ImapAccount $imapConfigId */
        $imapConfigId = $this->entityManager->getReference(ImapAccount::class, $this->getImapConfigId()->id());
        return $imapConfigId;
    }
}
