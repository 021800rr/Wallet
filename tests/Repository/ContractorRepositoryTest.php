<?php

namespace App\Tests\Repository;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContractorRepositoryTest extends KernelTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testFindAll(): void
    {
        $contractors = $this->contractorRepository->findAll();
        $this->assertSame(5, count($contractors));
    }

    public function testGetInternalTransferOwner(): void
    {
        $contractor = $this->internalTransferOwner;

        $this->assertSame(5, $contractor->getId());
        $this->assertSame("Przelew własny", $contractor->getDescription());
    }
}
