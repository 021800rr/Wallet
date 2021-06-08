<?php

namespace App\Tests\Repository;

use App\Entity\Contractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContractorRepositoryTest extends KernelTestCase
{
    /** @var Doctrine\ORM\EntityManager */
    private $entityManager;

    public function testFindAll(): void
    {
        $contractors = $this->entityManager
            ->getRepository(Contractor::class)
            ->findAll();
        $this->assertSame(5, count($contractors));
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
