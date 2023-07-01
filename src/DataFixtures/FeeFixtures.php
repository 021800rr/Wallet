<?php

namespace App\DataFixtures;

use App\Entity\Contractor;
use App\Entity\Fee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FeeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $fee = new Fee();
        $fee->setDate(2);
        $fee->setAmount(-52);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::NETFLIX);
        $fee->setContractor($contractor);
        $manager->persist($fee);

        $fee = new Fee();
        $fee->setDate(4);
        $fee->setAmount(-19.99);
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::SPOTIFY);
        $fee->setContractor($contractor);
        $manager->persist($fee);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ContractorFixtures::class];
    }
}
