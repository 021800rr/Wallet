<?php

namespace App\DataFixtures;

use App\Entity\Eur;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EuroWalletFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $eur = new Eur();
        $eur->setDate(new DateTime('2021-10-30'));
        $eur->setAmount(10.01);
        $eur->setBalance(10.01);
        $eur->setContractor($this->getReference(ContractorFixtures::INTERNAL));
        $eur->setDescription('wpłata 1');
        $eur->setIsConsistent(true);
        $manager->persist($eur);

        $eur = new Eur();
        $eur->setDate(new DateTime('2021-11-04'));
        $eur->setAmount(20.02);
        $eur->setBalance(30.03);
        $eur->setContractor($this->getReference(ContractorFixtures::INTERNAL));

        $manager->persist($eur);

        $eur = new Eur();
        $eur->setDate(new DateTime('2021-11-26'));
        $eur->setAmount(40.04);
        $eur->setBalance(70.07);
        $eur->setContractor($this->getReference(ContractorFixtures::INTERNAL));

        $manager->persist($eur);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ContractorFixtures::class];
    }
}
