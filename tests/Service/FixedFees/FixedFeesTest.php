<?php

namespace App\Tests\Service\FixedFees;

use App\Entity\Wallet;
use App\Service\FixedFees\FixedFees;
use App\Tests\SetUp;
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

        /** @var Wallet[] $transactions */
        $transactions = $this->walletRepository->findAll();
        $this->assertSame(170.00, $transactions[0]->getBalance());

        $fixedFees->insert();

        /** @var Wallet[] $transactions */
        $transactions = $this->walletRepository->findAll();
        $this->assertSame(98.01, $transactions[0]->getBalance());
    }
}
