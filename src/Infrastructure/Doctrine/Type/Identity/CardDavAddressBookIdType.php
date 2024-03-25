<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 17/03/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type\Identity;

use App\Component\Shared\Identity\CardDavAddressBookId;
use Override;

/**
 * @extends AbstractIdentityType<CardDavAddressBookId>
 */
class CardDavAddressBookIdType extends AbstractIdentityType
{
    public const string NAME = 'card_dav_address_book_id';

    #[Override]
    protected function getIdentityFqcn(): string
    {
        return CardDavAddressBookId::class;
    }
}
