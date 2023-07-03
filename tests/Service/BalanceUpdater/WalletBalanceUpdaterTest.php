<?php

namespace App\Tests\Service\BalanceUpdater;

use App\Entity\Pln;
use App\Tests\SetUp;
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
        /** @var Pln[] $transactions */
        $transactions = $this->plnRepository->findAll();
        $this->assertSame(170.00, $transactions[0]->getBalance());

        $transaction = $transactions[1];
        $this->assertSame(-10.00, $transaction->getAmount());
        $transaction->setAmount(-20);
        $this->assertSame(-20.00, $transaction->getAmount());

        $this->walletFactory->create()->compute($this->plnRepository, $transaction->getId());

        /** @var Pln[] $transactions */
        $transactions = $this->plnRepository->findAll();
        $this->assertSame(160.00, $transactions[0]->getBalance());
    }
}
