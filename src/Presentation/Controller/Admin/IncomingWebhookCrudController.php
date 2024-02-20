<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\IncomingWebhook;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IncomingWebhookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return IncomingWebhook::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $replayAction = Action::new('webhook_replay', 'Replay')
            ->setIcon('fas fa-recycle')
            ->setCssClass('btn btn-secondary')
            ->linkToRoute(
                routeName: 'webhook_replay',
                routeParameters: static fn(IncomingWebhook $webhook) => ['webhookId' => (string) $webhook->getId()]
            );
        return $actions
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, $replayAction)
            ->add(Crud::PAGE_INDEX, $replayAction);
    }

    /**
     * @param string $pageName
     * @return iterable<FieldInterface>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('createdAt');
        yield TextField::new('source');
        yield TextField::new('event');
        yield ArrayField::new('payload')
            ->setTemplatePath('admin/field/json.html.twig')
            ->onlyOnDetail();
    }
}
