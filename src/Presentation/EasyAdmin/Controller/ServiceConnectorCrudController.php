<?php

namespace App\Presentation\EasyAdmin\Controller;

use App\Component\Shared\ValueObject\Service;
use App\Infrastructure\Doctrine\Entity\ServiceConnector;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

use function App\enum_to_choices;
use function App\new_uuid;

class ServiceConnectorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ServiceConnector::class;
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
            ChoiceField::new('service', 'services.field.service_type')
                ->setChoices(enum_to_choices(Service::class, 'services.available_services')),
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
        /** @var ServiceConnector $entity */
        $entity = parent::createEntity($entityFqcn);
        $entity->setAccessToken(new_uuid());
        return $entity;
    }
}
