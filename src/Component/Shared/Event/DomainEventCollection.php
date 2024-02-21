<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Component\Shared\Event;

class DomainEventCollection
{
    private static ?self $instance = null;

    /**
     * @param Event[] $events
     */
    private function __construct(
        private array $events
    ) {}

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self([]);
        }
        return self::$instance;
    }

    /**
     * @return Event[]
     */
    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    public function addDomainEvent(Event $event): void
    {
        $this->events[] = $event;
    }
}
