<?php

namespace App\Tests\Repository;

use App\Entity\Contractor;
use App\Repository\ContractorRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContractorRepositoryTest extends KernelTestCase
{
    use Setup;

    public function testFindAll(): void
    {
        $contractorRepository = $this->getRepository();
        $contractors = $contractorRepository->findAll();
        $this->assertSame(5, count($contractors));
    }

    public function testGetInternalTransferOwner(): void
    {
        $contractorRepository = $this->getRepository();
        $contractor = $contractorRepository->getInternalTransferOwner();

        $this->assertSame(5, $contractor->getId());
        $this->assertSame("Przelew własny", $contractor->getDescription());
    }

    private function getRepository(): ContractorRepository
    {
        /** @var ContractorRepository */
        return $this->entityManager->getRepository(Contractor::class);
    }
}
