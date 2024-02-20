<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Option;

use App\Component\Shared\Error\LogicError;
use App\Infrastructure\Doctrine\Entity\AccountAwareInterface;
use App\Infrastructure\Doctrine\Entity\SlugAwareInterface;
use Doctrine\ORM\Mapping\GeneratedValue;
use ReflectionClass;

/**
 * @property-read bool $filterByAccount
 * @property-read bool $onCreateGenerateUuid
 * @property-read bool $onCreateAssignAccount
 * @property-read bool $detailPage
 * @property-read bool $onCreateGenerateSlug
 */
class CrudConfigDto
{
    private bool $filterByAccount = false;
    private bool $onCreateGenerateUuid = false;
    private bool $onCreateAssignAccount = false;
    private bool $onCreateGenerateSlug = false;
    private bool $detailPage = true;

    public function setFilterByAccount(): CrudConfigDto
    {
        $this->filterByAccount = true;
        return $this;
    }

    public function setOnCreateGenerateUuid(): CrudConfigDto
    {
        $this->onCreateGenerateUuid = true;
        return $this;
    }

    public function setOnCreateAssignAccount(): CrudConfigDto
    {
        $this->onCreateAssignAccount = true;
        return $this;
    }

    public function disableDetailPage(): CrudConfigDto
    {
        $this->detailPage = false;
        return $this;
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            /** @phpstan-ignore-next-line  */
            return $this->$name;
        }
        throw new LogicError("Undefined property: {$name}");
    }

    /**
     * @param class-string $entityFqcn
     * @throws \ReflectionException
     */
    public static function fromEntityFqcn(string $entityFqcn): CrudConfigDto
    {
        $config = new CrudConfigDto();
        if (is_a($entityFqcn, AccountAwareInterface::class, true)) {
            $config->setFilterByAccount()
                ->setOnCreateAssignAccount();
        }

        if (is_a($entityFqcn, SlugAwareInterface::class, true)) {
            $config->onCreateGenerateSlug = true;
        }

        $reflection = (new ReflectionClass($entityFqcn))
            ->getProperty('id')
            ->getAttributes(GeneratedValue::class);
        if (count($reflection) > 0) {
            $arguments = ($reflection[0]->getArguments());
            if (isset($arguments['strategy']) && $arguments['strategy'] === 'NONE') {
                $config->setOnCreateGenerateUuid();
            }
        }
        return $config;
    }
}
