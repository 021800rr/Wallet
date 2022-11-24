<?php

namespace App\DataFixtures;

use App\Entity\Wallet;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WalletFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $wallet = new Wallet();
        $wallet->setDate(new DateTime('2021-05-10'));
        $wallet->setAmount(-1);
        $wallet->setBalance(200.00);
        $wallet->setContractor($this->getReference(ContractorFixtures::MEDIA_EXPERT));
        $wallet->setDescription('z przeniesienia');
        $wallet->setIsConsistent(true);
        $manager->persist($wallet);

        $wallet = new Wallet();
        $wallet->setDate(new DateTime('2021-05-12'));
        $wallet->setAmount(-10.00);
        $wallet->setBalance(191.00);
        $wallet->setContractor($this->getReference(ContractorFixtures::ALLEGRO));

        $manager->persist($wallet);

        $wallet = new Wallet();
        $wallet->setDate(new DateTime('2021-05-13'));
        $wallet->setAmount(-20.);
        $wallet->setBalance(170.00);
        $wallet->setContractor($this->getReference(ContractorFixtures::ALLEGRO));

        $manager->persist($wallet);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ContractorFixtures::class];
    }
}
