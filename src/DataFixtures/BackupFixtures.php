<?php

namespace App\DataFixtures;

use App\Entity\Backup;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BackupFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $backup = new Backup();
        $backup->setDate(new DateTime('2021-05-01'));
        $backup->setYearMonth('2021-05');
        $backup->setAmount(100);
        $backup->setRetiring(50);
        $backup->setHoliday(50);
        $backup->setBalance(100.00);
        $backup->setContractor($this->getReference(ContractorFixtures::INTERNAL));
        $backup->setDescription('a fresh one');
        $manager->persist($backup);

        $backup = new Backup();
        $backup->setDate(new DateTime('2021-05-02'));
        $backup->setYearMonth('2021-05');
        $backup->setAmount(200);
        $backup->setRetiring(150);
        $backup->setHoliday(150);
        $backup->setBalance(300.00);
        $backup->setContractor($this->getReference(ContractorFixtures::INTERNAL));
        $manager->persist($backup);

        $backup = new Backup();
        $backup->setDate(new DateTime('2021-05-03'));
        $backup->setYearMonth('2021-05');
        $backup->setAmount(300);
        $backup->setRetiring(300);
        $backup->setHoliday(300);
        $backup->setBalance(600.00);
        $backup->setContractor($this->getReference(ContractorFixtures::INTERNAL));
        $manager->persist($backup);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ContractorFixtures::class];
    }
}
