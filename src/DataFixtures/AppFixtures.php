<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setUsername('jeckel')
            ->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'testtest'
                )
            );
        // ->setPassword('$2y$13$g7Gbh5seMaaaCI.uSNFfp.Q6QYhyIwfp7pWxzmaVXg6KzLnpPAvgS');

        $manager->persist($user);
        $manager->flush();
    }
}
