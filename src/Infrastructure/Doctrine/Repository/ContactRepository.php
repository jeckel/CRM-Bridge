<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Component\Shared\ValueObject\EmailType;
use App\Infrastructure\Doctrine\Entity\CardDavAddressBook;
use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\EntityModel\Company;
use App\Infrastructure\Doctrine\EntityModel\ContactEmail;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends AbstractEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function findByEmail(string $email): ?Contact
    {
        /** @var null|Contact $contact */
        $contact = $this->createQueryBuilder('u')
            ->join('u.emailAddresses', 'e')
            ->where('e.emailAddress = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $contact;
    }

    public function createQueryList(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select(
                'c.id',
                'c.displayName',
                'c.phoneNumber',
                'co.name as companyName',
                'e.emailAddress',
                'a.name as addressBook'
            )
            ->innerJoin(CardDavAddressBook::class, 'a', 'WITH', 'c.addressBook = a.id')
            ->leftJoin(Company::class, 'co', 'WITH', 'co.id = c.company')
            ->leftJoin(ContactEmail::class, 'e', 'WITH', 'c.id = e.contact AND e.emailType = :emailType')
            ->setParameter('emailType', EmailType::PRIMARY->value);
    }

    public function remove(Contact $contact): void
    {
        $this->getEntityManager()->remove($contact);
        $this->getEntityManager()->flush();
    }
}
