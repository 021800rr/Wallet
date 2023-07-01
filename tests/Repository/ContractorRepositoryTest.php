<?php

namespace App\Tests\Repository;

use App\Tests\SetUp;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContractorRepositoryTest extends KernelTestCase
{
    use SetUp;

    public function testFindAll(): void
    {
        $contractors = $this->contractorRepository->findAll();
        $this->assertSame(5, count($contractors));
    }

    /**
     * @throws Exception
     */
    public function testGetInternalTransferOwner(): void
    {
        $contractor = $this->contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');

        $this->assertSame(5, $contractor->getId());
        $this->assertSame("Przelew własny", $contractor->getDescription());
    }
}
