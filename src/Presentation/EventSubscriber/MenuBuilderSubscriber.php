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
        $admin = new MenuItemModel('admin', 'menu.admin', 'admin', [], 'fa fa-wrench');
//        $admin->addChild(
//            new MenuItemModel('AdminDashboard', 'menu.dashboard', 'admin', [], 'fas fa-rss-square')
//        );
//        $admin->addChild(
//            new MenuItemModel('Workers', 'menu.workers', 'worker_list', [], 'fa fa-helmet-safety')
//        );

        $event->addItem(new MenuItemModel('WebMail', 'menu.webmail', 'webmail_index', [], 'fa fa-inbox'));
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
