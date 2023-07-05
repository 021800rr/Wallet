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
        // id = 1; row = 5
        $pln = new Pln();
        $pln->setDate(new DateTime('2021-01-13'));
        $pln->setAmount(-1);
        $pln->setBalance(200.00);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::MEDIA_EXPERT);
        $pln->setContractor($contractor);
        $pln->setDescription('z przeniesienia');
        $pln->setIsConsistent(true);
        $manager->persist($pln);

        // id = 2; row = 4
        $pln = new Pln();
        $pln->setDate(new DateTime('2021-02-13'));
        $pln->setAmount(-10);
        $pln->setBalance(190.00);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::MEDIA_EXPERT);
        $pln->setContractor($contractor);
        $pln->setIsConsistent(true);
        $manager->persist($pln);

        // id = 3; row = 3
        $pln = new Pln();
        $pln->setDate(new DateTime('2021-03-13'));
        $pln->setAmount(-20);
        $pln->setBalance(170.00);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::MEDIA_EXPERT);
        $pln->setContractor($contractor);
        $pln->setIsConsistent(true);
        $manager->persist($pln);

        // id = 4; row = 2
        $pln = new Pln();
        $pln->setDate(new DateTime('2021-04-13'));
        $pln->setAmount(-30.00);
        $pln->setBalance(140.00);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::ALLEGRO);
        $pln->setContractor($contractor);
        $manager->persist($pln);

        // id = 5; row = 1
        $pln = new Pln();
        $pln->setDate(new DateTime('2021-05-13'));
        $pln->setAmount(-40.);
        $pln->setBalance(100.00);
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
