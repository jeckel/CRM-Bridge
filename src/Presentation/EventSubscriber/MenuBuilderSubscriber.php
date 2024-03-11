<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 29/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\EventSubscriber;

use KevinPapst\TablerBundle\Event\MenuEvent;
use KevinPapst\TablerBundle\Model\MenuItemInterface;
use KevinPapst\TablerBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuBuilderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvent::class => ['onSetupMenu', 100],
        ];
    }

    public function onSetupMenu(MenuEvent $event): void
    {
        $admin = new MenuItemModel('admin', 'menu.admin', 'admin', [], 'ti ti-settings');
        $admin->addChild(
            new MenuItemModel('Workers', 'menu.workers', 'worker_list', [], 'ti ti-propeller')
        );
        $admin->addChild(
            new MenuItemModel('Setup', 'menu.setup', 'setup.index', [], 'ti ti-settings')
        );
        $event->addItem(new MenuItemModel('WebMail', 'menu.webmail', 'webmail_index', [], 'ti ti-inbox'));
        $event->addItem(new MenuItemModel('Contacts', 'menu.contacts', 'contact.index', [], 'ti ti-id'));
        $event->addItem($admin);

        /** @var string $route */
        $route = $event->getRequest()->get('_route');
        $this->activateByRoute(
            $route,
            $event->getItems()
        );
    }

    /**
     * @param string $route
     * @param MenuItemInterface[] $items
     */
    protected function activateByRoute(string $route, array $items): void
    {
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
                return;
            }
            if ($item->getRoute() === $route) {
                $item->setIsActive(true);
            }
        }
    }
}
