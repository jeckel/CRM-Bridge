<?php

namespace App\Presentation\EasyAdmin\Controller;

use App\Infrastructure\Doctrine\Entity\Configuration;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Configuration::class;
    }

    /**
     * @return iterable<FieldInterface>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('property', 'Propriété')
            ->setTemplatePath('admin/field/config_property.html.twig');
        yield TextField::new('value', 'Valeur');
    }
}
