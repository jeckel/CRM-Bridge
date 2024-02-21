<?php

namespace App\DataFixtures;

use App\Infrastructure\Doctrine\Entity\Account;
use App\Infrastructure\Doctrine\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function App\new_uuid;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $luke = new User();
        $luke->setRoles(['ROLE_USER'])
            ->setUsername('luke')
            ->setFirstname('Luke')
            ->setLastname('Skywalker')
            ->setEmail('luke@rebel-alliance.com')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $luke,
                    'password'
                )
            );
        $manager->persist($luke);

        $leia = new User();
        $leia->setRoles(['ROLE_ADMIN'])
            ->setUsername('leia')
            ->setFirstname('Leia')
            ->setLastname('Organa')
            ->setEmail('leia@rebel-alliance.com')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $leia,
                    'password'
                )
            );
        $manager->persist($leia);

        $vader = new User();
        $vader->setRoles(['ROLE_USER'])
            ->setUsername('vader')
            ->setFirstname('Darth')
            ->setLastname('Vader')
            ->setEmail('darth@galactic-empire.com')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $vader,
                    'password'
                )
            );
        $manager->persist($vader);

        $palpatine = new User();
        $palpatine->setRoles(['ROLE_ADMIN'])
            ->setUsername('palpatine')
            ->setFirstname('Sheev')
            ->setLastname('Palpatine')
            ->setEmail('palpatine@galactic-empire.com')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $palpatine,
                    'password'
                )
            );
        $manager->persist($palpatine);

        $jeckel = new User();
        $jeckel->setRoles(['ROLE_SUPER_ADMIN'])
            ->setUsername('jeckel')
            ->setFirstname('Julien')
            ->setLastname('Mercier-Rojas')
            ->setEmail('julien@jeckel-lab.com')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $jeckel,
                    'password'
                )
            );
        $manager->persist($jeckel);

        $manager->flush();
    }
}
