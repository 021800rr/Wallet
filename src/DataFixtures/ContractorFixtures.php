<?php

namespace App\DataFixtures;

use App\Entity\Contractor;
use App\Repository\ContractorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContractorFixtures extends Fixture
{
    public const MEDIA_EXPERT = 'Media Expert';
    public const ALLEGRO = 'Allegro';
    public const NETFLIX = 'Netflix';
    public const SPOTIFY = 'Spotify';
    public const INTERNAL = 'Internal';

    public function load(ObjectManager $manager): void
    {
        $mediaExpert = new Contractor();
        $mediaExpert->setDescription(self::MEDIA_EXPERT);
        $manager->persist($mediaExpert);

        $allegro = new Contractor();
        $allegro->setDescription(self::ALLEGRO);
        $manager->persist($allegro);

        $netflix = new Contractor();
        $netflix->setDescription(self::NETFLIX);
        $manager->persist($netflix);

        $spotify = new Contractor();
        $spotify->setDescription(self::SPOTIFY);
        $manager->persist($spotify);

        $internal = new Contractor();
        $internal->setDescription(ContractorRepository::INTERNAL_TRANSFER);
        $manager->persist($internal);

        $manager->flush();

        $this->addReference(self::MEDIA_EXPERT, $mediaExpert);
        $this->addReference(self::ALLEGRO, $allegro);
        $this->addReference(self::NETFLIX, $netflix);
        $this->addReference(self::SPOTIFY, $spotify);
        $this->addReference(self::INTERNAL, $internal);
    }
}
