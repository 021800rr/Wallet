<?php

namespace App\Tests\Repository;

use App\Entity\Contractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContractorRepositoryTest extends KernelTestCase
{
    use Setup;

    public function testFindAll(): void
    {
        $contractors = $this->entityManager
            ->getRepository(Contractor::class)
            ->findAll();
        $this->assertSame(5, count($contractors));
    }

    public function testGetInternalTransferOwner(): void
    {
        $contractor = $this->entityManager
            ->getRepository(Contractor::class)
            ->getInternalTransferOwner();

        $this->assertSame(5, $contractor->getId());
        $this->assertSame("Przelew własny", $contractor->getDescription());
    }
}
