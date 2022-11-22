<?php

namespace App\Tests\Repository;

use App\Entity\Contractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContractorRepositoryTest extends KernelTestCase
{
    use SetupTrait;

    public function testFindAll(): void
    {
        $contractors = $this->entityManager
            ->getRepository(Contractor::class)
            ->findAll();
        $this->assertSame(5, count($contractors));
    }
}
