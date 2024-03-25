<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/02/2024
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Component\Shared\ValueObject\Service;
use App\Infrastructure\Doctrine\Entity\Account;
use App\Infrastructure\Doctrine\Entity\ServiceConnector;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

class ServiceConnectorFixtures extends Fixture
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
//        $manager->persist(
//            (new ServiceConnector())
//            ->setService(Service::CAL_DOT_COM)
//            ->setEnabled(true)
//            ->setAccessToken('1c496f66-04ac-4671-a115-6b9c0f66111e')
//        );
//
//        $manager->persist(
//            (new ServiceConnector())
//            ->setService(Service::CAL_DOT_COM)
//            ->setEnabled(false)
//            ->setAccessToken('c4411ebb-7d61-4f94-9702-b681c15e7ae0')
//        );
//
//        $manager->persist(
//            (new ServiceConnector())
//                ->setService(Service::CAL_DOT_COM)
//                ->setEnabled(true)
//                ->setAccessToken('5fa36ef4-2baf-4129-aab0-ed3dabb2bf4c')
//        );

        $manager->flush();
    }
}
