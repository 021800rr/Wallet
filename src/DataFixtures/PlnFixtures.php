<?php

namespace App\DataFixtures;

use App\Entity\Contractor;
use App\Entity\Pln;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlnFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $pln = new Pln();
        $pln->setDate(new DateTime('2021-05-10'));
        $pln->setAmount(-1);
        $pln->setBalance(200.00);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::MEDIA_EXPERT);
        $pln->setContractor($contractor);
        $pln->setDescription('z przeniesienia');
        $pln->setIsConsistent(true);
        $manager->persist($pln);

        $pln = new Pln();
        $pln->setDate(new DateTime('2021-05-12'));
        $pln->setAmount(-10.00);
        $pln->setBalance(191.00);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::ALLEGRO);
        $pln->setContractor($contractor);

        $manager->persist($pln);

        $pln = new Pln();
        $pln->setDate(new DateTime('2021-05-13'));
        $pln->setAmount(-20.);
        $pln->setBalance(170.00);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::ALLEGRO);
        $pln->setContractor($contractor);

        $manager->persist($pln);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ContractorFixtures::class];
    }
}
