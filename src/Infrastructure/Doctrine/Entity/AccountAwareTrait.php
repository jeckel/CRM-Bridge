<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\AccountId;
use RuntimeException;

/**
 * @property Account|null $account
 */
trait AccountAwareTrait
{
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function getAccountOrFail(): Account
    {
        if (null === $this->account) {
            throw new RuntimeException('Account not set');
        }
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getAccountId(): AccountId
    {
        return $this->getAccountOrFail()->getAccountId();
    }
}
