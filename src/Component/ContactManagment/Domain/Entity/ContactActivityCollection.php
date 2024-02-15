<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Component\ContactManagment\Domain\Entity;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<ContactActivity>
 */
class ContactActivityCollection implements IteratorAggregate
{
    private bool $changed = false;

    /**
     * @param ContactActivity[] $activities
     */
    public function __construct(private array $activities = []) {}

    /**
     * @return Traversable<ContactActivity>
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->activities);
    }

    public function add(ContactActivity $activity): self
    {
        $this->activities[] = $activity;
        $this->changed = true;
        return $this;
    }

    public function hasChanged(): bool
    {
        return $this->changed;
    }
}
