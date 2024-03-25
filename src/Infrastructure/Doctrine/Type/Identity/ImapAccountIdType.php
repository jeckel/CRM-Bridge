<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 17/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type\Identity;

use App\Component\Shared\Identity\ImapAccountId;
use Override;

/**
 * @extends AbstractIdentityType<ImapAccountId>
 */
class ImapAccountIdType extends AbstractIdentityType
{
    public const string NAME = 'imap_account_id';

    #[Override]
    protected function getIdentityFqcn(): string
    {
        return ImapAccountId::class;
    }
}
