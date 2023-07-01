<?php

namespace App\DataFixtures;

use App\Entity\Chf;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChfWalletFixtures extends Fixture implements DependentFixtureInterface
{
    use InternalOwner;

    public function load(ObjectManager $manager): void
    {
        $contractor = $this->getInternalOwner();

        $chf = new Chf();
        $chf->setDate(new DateTime('2021-10-30'));
        $chf->setAmount(10.01);
        $chf->setBalance(10.01);
        $chf->setContractor($contractor);
        $chf->setDescription('wpłata 1');
        $chf->setIsConsistent(true);
        $manager->persist($chf);

        $chf = new Chf();
        $chf->setDate(new DateTime('2021-11-04'));
        $chf->setAmount(20.02);
        $chf->setBalance(30.03);
        $chf->setContractor($contractor);

        $manager->persist($chf);

        $chf = new Chf();
        $chf->setDate(new DateTime('2021-11-26'));
        $chf->setAmount(40.04);
        $chf->setBalance(70.07);
        $chf->setContractor($contractor);

        $manager->persist($chf);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ContractorFixtures::class];
    }
}
