<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Entity;

use App\Component\Shared\ValueObject\Email;
use App\Component\Shared\ValueObject\EmailType;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, array{email: Email, type: EmailType}>
 */
class EmailAddressCollection implements IteratorAggregate
{
    /**
     * @param array<string, array{email: Email, type: EmailType}> $addresses
     */
    public function __construct(private array $addresses = []) {}

    /**
     * @return Traversable<string, array{email: Email, type: EmailType}>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->addresses);
    }

    public function setPrimary(Email $email): self
    {
        if (! $this->has($email)) {
            $this->addresses[(string) $email] = ['email' => $email, 'type' => EmailType::PRIMARY];
        }
        foreach($this->addresses as $emailAddress) {
            $emailAddress['type'] = ($emailAddress['email']->equals($email)) ? EmailType::PRIMARY : EmailType::SECONDARY;
        }
        return $this;
    }

    public function add(Email $email): self
    {
        if (! $this->has($email)) {
            $this->addresses[(string) $email] = [
                'email' => $email,
                'type' => count($this->addresses) > 0 ? EmailType::SECONDARY : EmailType::PRIMARY,
            ];
        }
        return $this;
    }

    public function has(Email $email): bool
    {
        return array_key_exists((string) $email, $this->addresses);
    }
}
