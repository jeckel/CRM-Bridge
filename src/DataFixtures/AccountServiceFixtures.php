<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/02/2024
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Component\Shared\ValueObject\Service;
use App\Infrastructure\Doctrine\Entity\Account;
use App\Infrastructure\Doctrine\Entity\AccountService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

class AccountServiceFixtures extends Fixture implements DependentFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager)
    {
        $manager->persist(
            (new AccountService())
            ->setService(Service::CAL_DOT_COM->value)
            ->setAccount($this->getReference(AccountFixtures::REBEL_ALLIANCE, Account::class))
            ->setEnabled(true)
            ->setAccessToken('1c496f66-04ac-4671-a115-6b9c0f66111e')
        );

        $manager->persist(
            (new AccountService())
            ->setService(Service::CAL_DOT_COM->value)
            ->setAccount($this->getReference(AccountFixtures::GALACTIC_EMPIRE, Account::class))
            ->setEnabled(false)
            ->setAccessToken('c4411ebb-7d61-4f94-9702-b681c15e7ae0')
        );

        $manager->persist(
            (new AccountService())
                ->setService(Service::CAL_DOT_COM->value)
                ->setAccount($this->getReference(AccountFixtures::SUPER_ADMIN, Account::class))
                ->setEnabled(true)
                ->setAccessToken('5fa36ef4-2baf-4129-aab0-ed3dabb2bf4c')
        );

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
