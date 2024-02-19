<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\AccountAwareInterface;
use App\Infrastructure\Doctrine\Entity\User;
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

use function App\new_uuid;

abstract class AbstractCrudController extends EAAbstractCrudController
{
    private const string
        OPTION_FILTER_BY_ACCOUNT = 'filter_by_account',
    OPTION_NEW_GENERATE_UUID = 'new_generate_uuid',
    OPTION_NEW_ASSIGN_ACCOUNT = 'new_assign_account',
    OPTION_ENABLE_DETAIL_PAGE = 'enable_detail_page';

    /**
     * @var array<string, bool>
     */
    private array $options = [
        self::OPTION_FILTER_BY_ACCOUNT => false,
        self::OPTION_NEW_GENERATE_UUID => false,
        self::OPTION_NEW_ASSIGN_ACCOUNT => false,
        self::OPTION_ENABLE_DETAIL_PAGE => false,
    ];

    protected function enableFilterByAccount(): self
    {
        $this->options[self::OPTION_FILTER_BY_ACCOUNT] = true;
        return $this;
    }

    protected function enableNewGenerateUuid(): self
    {
        $this->options[self::OPTION_NEW_GENERATE_UUID] = true;
        return $this;
    }

    protected function enableNewAssignAccount(): self
    {
        $this->options[self::OPTION_NEW_ASSIGN_ACCOUNT] = true;
        return $this;
    }

    protected function enableDetailPage(): self
    {
        $this->options[self::OPTION_ENABLE_DETAIL_PAGE] = true;
        return $this;
    }

    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        if ($this->options[self::OPTION_ENABLE_DETAIL_PAGE]) {
            $actions
                ->add(Crud::PAGE_INDEX, Action::DETAIL)
                ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                    return $action->setIcon('fa fa-eye')
                        ->setCssClass('btn btn-secondary');
                });
        }
        // Setup EDIT button on index page
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, static function (Action $action) {
            return $action->setIcon('fa fa-pencil')
                ->setCssClass('btn btn-secondary');
        });
        // Setup DELETE button on index page
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action) {
            return $action->setIcon('fa fa-trash-o')
                ->setCssClass('btn btn-secondary text-danger');
        });
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
        if ($this->options[self::OPTION_FILTER_BY_ACCOUNT]) {
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
        if ($this->options[self::OPTION_FILTER_BY_ACCOUNT]) {
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
        if ($this->options[self::OPTION_NEW_GENERATE_UUID]) {
            $entity->setId(new_uuid());
        }

        if ($this->options[self::OPTION_NEW_ASSIGN_ACCOUNT] && is_a($entity, AccountAwareInterface::class)) {
            /** @var User $user */
            $user = $this->getUser();
            $entity->setAccount($user->getAccount());
        }
        return $entity;
    }
}
