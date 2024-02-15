<?php

namespace App\DataFixtures;

use App\Infrastructure\Doctrine\Entity\Account;
use App\Infrastructure\Doctrine\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function App\new_uuid;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $rebelAlliance = (new Account())
            ->setId(new_uuid())
            ->setName('Rebel Alliance');
        $manager->persist($rebelAlliance);

        $empire = (new Account())
            ->setId(new_uuid())
            ->setName('Galactic Empire');
        $manager->persist($empire);

        $jeckelLab = (new Account())
            ->setId(new_uuid())
            ->setName('Jeckel-Lab');
        $manager->persist($jeckelLab);

        $luke = new User();
        $luke->setRoles(['ROLE_USER'])
            ->setUsername('luke')
            ->setFirstname('Luke')
            ->setLastname('Skywalker')
            ->setEmail('luke@rebel-alliance.com')
            ->setAccount($rebelAlliance)
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
            ->setAccount($rebelAlliance)
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
            ->setAccount($empire)
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
            ->setAccount($empire)
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
            ->setAccount($jeckelLab)
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
