<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Entity\IncomingWebhook;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class IncomingWebhookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return IncomingWebhook::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
//            ->setPageTitle(Crud::PAGE_INDEX, 'challenge.list.title')
//            ->setDefaultSort(['startAt' => 'DESC'])
            ->setDateTimeFormat('dd/MM/yyyy HH:mm:ss')
            ->setPaginatorPageSize(100)
            ->showEntityActionsInlined()
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                return $action->setIcon('fa fa-eye')
                    ->setLabel(false)
                    ->setCssClass('btn btn-secondary');
            })
//            ->update(Crud::PAGE_INDEX, Action::EDIT, static function (Action $action) {
//                return $action->setIcon('fa fa-pencil')
//                    ->setLabel(false)
//                    ->setCssClass('btn btn-secondary');
//            })
            ;
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
            ->setTemplatePath('admin/fields/json.html.twig')
            ->onlyOnDetail();
    }
}
