<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

namespace App\Infrastructure\Doctrine\Entity;

interface AccountAwareInterface
{
    public function getAccount(): ?Account;
    public function getAccountOrFail(): Account;
    public function setAccount(?Account $account): self;
}
