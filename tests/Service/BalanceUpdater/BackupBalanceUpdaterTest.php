<?php

namespace App\Tests\Service\BalanceUpdater;

use App\Service\BalanceUpdater\BackupBalanceUpdater;
use App\Tests\Service\SetUp;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BackupBalanceUpdaterTest extends KernelTestCase
{
    use SetUp;

    /**
     * @throws Exception
     */
    public function testCompute(): void
    {
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(300.00, $transactions[0]->getRetiring());
        $this->assertSame(300.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $transaction = $transactions[1];
        $this->assertSame(200.00, $transaction->getAmount());
        $transaction->setAmount($transaction->getAmount() - 10);
        $this->assertSame(190.00, $transaction->getAmount());

        $this->backupBalanceUpdater->compute($this->backupRepository, $transaction->getId());

        $transactions = $this->backupRepository->findAll();
        $this->assertSame(295.00, $transactions[0]->getRetiring());
        $this->assertSame(295.00, $transactions[0]->getHoliday());
        $this->assertSame(590.00, $transactions[0]->getBalance());
    }
}
