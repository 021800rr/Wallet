<?php

namespace App\Tests\Service\FixedFees;

use App\Entity\Pln;
use App\Service\FixedFees\FixedFees;
use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FixedFeesTest extends KernelTestCase
{
    use SetUp;

    public function testInsert(): void
    {
        $fixedFees = new FixedFees(
            $this->walletUpdater,
            $this->feeRepository,
            $this->plnRepository,
        );

        $fixedFees->insert();

        /** @var Pln[] $transactions */
        $transactions = $this->plnRepository->findAll();
        $this->assertSame(28.01, $transactions[0]->getBalance());
    }
}
