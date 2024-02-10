<?php

namespace App\DataFixtures;

use App\Infrastructure\Doctrine\Entity\Account;
use App\Infrastructure\Doctrine\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $account = (new Account())
            ->setId(Uuid::uuid4()->toString())
            ->setName('Rebel Alliance');

        $manager->persist($account);

        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setUsername('jeckel')
            ->setAccount($account)
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'testtest'
                )
            );

        $manager->persist($user);
        $manager->flush();
    }
}
