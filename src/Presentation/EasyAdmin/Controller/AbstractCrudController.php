<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\EasyAdmin\Controller;

use App\Presentation\EasyAdmin\Controller\Option\CrudConfigDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as EAAbstractCrudController;
use Override;
use ReflectionException;

use function App\new_uuid;

abstract class AbstractCrudController extends EAAbstractCrudController
{
    protected CrudConfigDto $config;

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        /** @var class-string $entityFqcn */
        $entityFqcn = static::getEntityFqcn();
        $this->config = CrudConfigDto::fromEntityFqcn($entityFqcn);
    }

    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        // Setup DELETE button on index page
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action) {
            return $action->setIcon('fa fa-trash-o')
                ->setCssClass('btn btn-secondary text-danger')
            ;
        });
        // Setup EDIT button on index page
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, static function (Action $action) {
            return $action->setIcon('fa fa-pencil')
                ->setCssClass('btn btn-secondary');
        });
        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);
        if ($this->config->detailPage) {
            $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                    return $action->setIcon('fa fa-eye')
                        ->setCssClass('btn btn-secondary');
                });
            $actions->remove(Crud::PAGE_INDEX, Action::EDIT);
            return $actions;
        }
        return $actions;
    }

    /**
     * @param string $entityFqcn
     * @return object
     */
    #[Override]
    public function createEntity(string $entityFqcn)
    {
        $entity = parent::createEntity($entityFqcn);
        if ($this->config->onCreateGenerateUuid) {
            $entity->setId(new_uuid());
        }

        return $entity;
    }
}
