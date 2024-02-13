<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\ValueObject;

use InvalidArgumentException;

final readonly class Email
{
    private string $email;
    public function __construct(string $email)
    {
        if (false === ($filteredEmail = filter_var($email, FILTER_VALIDATE_EMAIL))) {
            throw new InvalidArgumentException(sprintf('Invalid email [%s] provided', $email));
        }
        $this->email = $filteredEmail;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }
}
