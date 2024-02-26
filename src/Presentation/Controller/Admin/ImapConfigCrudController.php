<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ImapConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImapAccount::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'config.imap.title.index')
            ->setPageTitle(Crud::PAGE_NEW, 'config.imap.title.new');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'config.imap.field.name');
        yield TextField::new('uri', 'config.imap.field.uri');
        yield TextField::new('login', 'config.imap.field.login');
        yield Field::new('password', 'config.imap.field.password')
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW);
        yield ArrayField::new('folders', 'config.imap.field.synced_folders')
            ->hideOnForm();
    }
}
