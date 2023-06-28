<?php

namespace App\Tests\Service\BalanceUpdater;

use App\Tests\Service\SetUp;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletBalanceUpdaterTest extends KernelTestCase
{
    use SetUp;

    /**
     * @throws Exception
     */
    public function testCompute(): void
    {
        $transactions = $this->walletRepository->findAll();
        $this->assertSame(170.00, $transactions[0]->getBalance());

        $transaction = $transactions[1];
        $this->assertSame(-10.00, $transaction->getAmount());
        $transaction->setAmount(-20);
        $this->assertSame(-20.00, $transaction->getAmount());

        $this->walletFactory->create()->compute($this->walletRepository, $transaction->getId());

        $transactions = $this->walletRepository->findAll();
        $this->assertSame(160.00, $transactions[0]->getBalance());
    }
}
