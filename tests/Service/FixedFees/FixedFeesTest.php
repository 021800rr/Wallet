<?php

namespace App\Tests\Service\FixedFees;

use App\Entity\Wallet;
use App\Service\FixedFees\FixedFees;
use App\Tests\Service\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FixedFeesTest extends KernelTestCase
{
    use SetUp;

    public function testInsert(): void
    {
        $walletRepository = $this->entityManager->getRepository(Wallet::class);

        $transactions = $walletRepository->findAll();
        $oldBalance = ($transactions[0])->getBalance();
        $this->assertSame(170.00, $oldBalance);

        $fixedFees = new FixedFees(
            $this->entityManager,
            $this->feeRepository,
            $this->walletBalanceUpdater,
            $this->walletRepository,
        );

        $fixedFees->insert();

        $transactions = $walletRepository->findAll();
        $oldBalance = ($transactions[0])->getBalance();
        $this->assertSame(98.01, $oldBalance);

    }
}
