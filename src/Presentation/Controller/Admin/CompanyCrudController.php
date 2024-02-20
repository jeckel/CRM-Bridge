<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Ramsey\Uuid\Uuid;

class CompanyCrudController extends AbstractCrudController
{
    public function __construct()
    {
        $this->enableFilterByAccount()
            ->enableNewGenerateUuid()
            ->enableNewAssignAccount()
            ->enableDetailPage()
        ;
    }

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
    //            TextField::new('espoCompanyId'),
    //            AssociationField::new('contacts'),
                AssociationField::new('account', 'config.field.account')
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
            TextField::new('espoCompanyId'),
            AssociationField::new('contacts'),
            AssociationField::new('account', 'config.field.account')
                ->setPermission('ROLE_SUPER_ADMIN')
        ];
    }

    /**
     * @return Company
     */
    #[\Override]
    public function createEntity(string $entityFqcn)
    {
        /** @var Company $company */
        $company = parent::createEntity($entityFqcn);
        $company->setId(Uuid::uuid4()->toString());
        return $company;
    }
}
