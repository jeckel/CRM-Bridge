<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Error;

use App\Component\Shared\Error\LogicError;

class VCardUriChangedError extends LogicError {}
