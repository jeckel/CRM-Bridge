<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/02/2024 08:57
 */
declare(strict_types=1);

namespace App\Component\Shared\Helper;

use App\Component\Shared\Error\LogicError;
use App\Component\Shared\Identity\AccountId;
use App\Infrastructure\Doctrine\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class ContextManager
{
    private ?AccountId $accountId = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getAccountId(): AccountId
    {
        if (null === $this->accountId) {
            throw new LogicError('Account not defined yet');
        }
        return $this->accountId;
    }

    public function setAccountId(?AccountId $accountId): void
    {
        $this->accountId = $accountId;
    }

    /**
     * @throws ORMException
     */
    public function getAccountReference(): Account
    {
        /** @var Account $account */
        $account = $this->entityManager->getReference(Account::class, $this->getAccountId()->id());
        return $account;
    }
}
