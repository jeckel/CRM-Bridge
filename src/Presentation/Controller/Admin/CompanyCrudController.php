<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Ramsey\Uuid\Uuid;

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
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('espoCompanyId'),
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
