<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === 'create_company_from_contact') {
            return [
                TextField::new('name')
            ];
        }
        if ($pageName === Crud::PAGE_DETAIL) {
            return [
                FormField::addTab('company.tab.summary', 'fa fa-building'),
                TextField::new('name', 'company.field.name'),
                TextField::new('slug', 'company.field.slug')
                    ->setPermission('ROLE_SUPER_ADMIN'),
                FormField::addTab('company.tab.contacts', 'fas fa-id-card'),
                AssociationField::new('contacts')
                    ->setLabel(false)
                    ->setSortProperty('display_name')
                    ->setTemplatePath('admin/field/contact_list.html.twig'),
            ];
        }
        return [
            TextField::new('name'),
            TextField::new('slug')
                ->hideOnIndex()
                ->hideWhenCreating(),
            AssociationField::new('contacts')
                ->hideOnForm(),
        ];
    }
}
