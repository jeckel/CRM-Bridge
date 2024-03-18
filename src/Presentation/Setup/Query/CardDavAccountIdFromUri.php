<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Setup\Query;

use App\Component\Shared\Identity\CardDavAccountId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use LogicException;

readonly class CardDavAccountIdFromUri
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(string $uri): CardDavAccountId
    {
        /** @var null|array{id: CardDavAccountId} $result */
        $result = $this->entityManager->createQuery(
            'SELECT c.id
            FROM App\Component\CardDav\Domain\Entity\CardDavAccount c
            WHERE c.uri = :uri'
        )
            ->setParameter('uri', $uri)
            ->getOneOrNullResult()
        ;
        if (null === $result) {
            throw new LogicException('CardDav account not found');
        }
        return $result['id'];
    }
}
