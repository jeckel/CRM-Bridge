<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\AccountAwareInterface;
use App\Infrastructure\Doctrine\Entity\SlugAwareInterface;
use App\Infrastructure\Doctrine\Entity\User;
use App\Presentation\Controller\Admin\Option\CrudConfigDto;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as EAAbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Override;
use ReflectionException;

use function App\new_uuid;
use function App\slug;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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

    #[Override]
    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $queryBuilder = parent::createIndexQueryBuilder(
            $searchDto,
            $entityDto,
            $fields,
            $filters
        );
        if ($this->config->filterByAccount) {
            /** @var User $user */
            $user = $this->getUser();
            if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                return $queryBuilder;
            }
            $queryBuilder->andWhere('entity.account = :accountId')
                ->setParameter('accountId', $user->getAccountOrFail()->getId());
        }
        return $queryBuilder;
    }

    #[Override]
    public function configureFilters(Filters $filters): Filters
    {
        if ($this->config->filterByAccount) {
            /** @var User $user */
            $user = $this->getUser();
            if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                $filters->add('account');
            }
        }
        return $filters;
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

        if ($this->config->onCreateAssignAccount && is_a($entity, AccountAwareInterface::class)) {
            /** @var User $user */
            $user = $this->getUser();
            $entity->setAccount($user->getAccount());
        }
        return $entity;
    }

    // @phpstan-ignore-next-line
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($this->config->onCreateGenerateSlug) {
            /** @var SlugAwareInterface $entityInstance */
            $entityInstance->setSlug(slug($entityInstance->getSlugSource()));
        }
        parent::persistEntity($entityManager, $entityInstance);
    }
}
