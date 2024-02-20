<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/02/2024
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Infrastructure\Doctrine\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function App\new_uuid;

class AccountFixtures extends Fixture
{
    public const string REBEL_ALLIANCE = 'account-rebel-alliance';
    public const string GALACTIC_EMPIRE = 'account-galactic-empire';
    public const string SUPER_ADMIN = 'account-super-admin';

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

        $manager->flush();

        $this->addReference(self::REBEL_ALLIANCE, $rebelAlliance);
        $this->addReference(self::GALACTIC_EMPIRE, $empire);
        $this->addReference(self::SUPER_ADMIN, $jeckelLab);
    }
}
