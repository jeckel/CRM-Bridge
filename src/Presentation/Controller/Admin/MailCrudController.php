<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\Mail;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mail::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $sync = Action::new('imap_sync', 'imap.action.sync')
            ->linkToRoute(routeName: 'imap_sync')
            ->setIcon('fa fa-sync')
            ->setCssClass('btn btn-success')
            ->createAsGlobalAction();
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $sync)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                return $action->setIcon('fa fa-eye')
                    ->setCssClass('btn btn-secondary');
            });
    }
}
