<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @template T of object
 * @extends ServiceEntityRepository<T>
 */
abstract class AbstractEntityRepository extends ServiceEntityRepository
{
    /**
     * @param string|int $id
     * @return T
     * @throws EntityNotFoundException
     */
    public function getById(string|int $id)
    {
        return $this->find($id) ?? throw new EntityNotFoundException();
    }

    /**
     * @param T $entity
     */
    public function persist(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
