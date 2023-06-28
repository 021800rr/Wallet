<?php

namespace App\Tests\Service\FixedFees;

use App\Service\FixedFees\FixedFees;
use App\Tests\Service\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FixedFeesTest extends KernelTestCase
{
    use SetUp;

    public function testInsert(): void
    {
        $fixedFees = new FixedFees(
            $this->feeRepository,
            $this->walletFactory,
            $this->walletRepository,
        );

        $transactions = $this->walletRepository->findAll();
        $this->assertSame(170.00, $transactions[0]->getBalance());

        $fixedFees->insert();

        $transactions = $this->walletRepository->findAll();
        $this->assertSame(98.01, $transactions[0]->getBalance());
    }
}
