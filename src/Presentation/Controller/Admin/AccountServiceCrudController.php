<?php

namespace App\Presentation\Controller\Admin;

use App\Component\Shared\ValueObject\Service;
use App\Infrastructure\Doctrine\Entity\AccountService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

use function App\new_uuid;

class AccountServiceCrudController extends AbstractCrudController
{
    public function __construct()
    {
        $this->enableFilterByAccount()
            ->enableNewAssignAccount()
            ->enableDetailPage();
    }

    public static function getEntityFqcn(): string
    {
        return AccountService::class;
    }

    #[Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('services.label.singular')
            ->setEntityLabelInPlural('services.label.plural')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('account', 'config.field.account')
                ->setPermission('ROLE_SUPER_ADMIN')
                ->hideOnForm(),
            ChoiceField::new('service', 'services.field.service_type')
                ->setChoices(['services.available_services.' . Service::CAL_DOT_COM->name => Service::CAL_DOT_COM->value]),
            TextField::new('accessToken', 'services.field.access_token')
                ->onlyOnDetail(),
            BooleanField::new('enabled', 'services.field.enabled'),
        ];
    }

    /**
     * @param string $entityFqcn
     * @return object
     */
    #[Override]
    public function createEntity(string $entityFqcn)
    {
        /** @var AccountService $entity */
        $entity = parent::createEntity($entityFqcn);
        $entity->setAccessToken(new_uuid());
        return $entity;
    }
}
