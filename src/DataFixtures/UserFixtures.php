<?php

namespace App\DataFixtures;

use App\Component\Security\Domain\Entity\User;
use App\Component\Shared\Identity\UserId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $luke = new User(
            id: UserId::new(),
            username: 'luke',
            email: new Email('luke@rebel-alliance.com'),
            firstname: 'Luke',
            lastname: 'Skywalker',
            roles: ['ROLE_USER'],
        );
        $luke->setPassword(
            $this->passwordHasher->hashPassword(
                $luke,
                'password'
            )
        );
        $manager->persist($luke);

        $jeckel = new User(
            id: UserId::new(),
            username: 'jeckel',
            email: new Email('julien@jeckel-lab.com'),
            firstname: 'Julien',
            lastname: 'Mercier-Rojas',
            roles: ['ROLE_SUPER_ADMIN', 'ROLE_USER'],
        );
        $jeckel->setPassword(
            $this->passwordHasher->hashPassword(
                $jeckel,
                'password'
            )
        );
        $manager->persist($jeckel);

        $manager->flush();
    }
}
