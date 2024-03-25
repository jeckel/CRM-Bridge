<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 17/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type\Identity;

use App\Component\Shared\Identity\ImapMailId;
use Override;

/**
 * @extends AbstractIdentityType<ImapMailId>
 */
class ImapMailIdType extends AbstractIdentityType
{
    public const string NAME = 'imap_mail_id';

    #[Override]
    protected function getIdentityFqcn(): string
    {
        return ImapMailId::class;
    }
}
