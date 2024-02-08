<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Domain\Component\ContactManagment\Port;

use App\ValueObject\Email;

interface VCard
{
    public function firstName(): ?string;

    public function lastName(): ?string;

    public function displayName(): string;

    public function email(): ?Email;

    public function phoneNumber(): ?string;

    public function company(): ?string;

    public function vCardUri(): string;

    public function vCardEtag(): string;
}
