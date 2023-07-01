<?php

namespace App\Tests\Service\BalanceUpdater;

use App\Entity\Backup;
use App\Tests\SetUp;
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
        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(300.00, $transactions[0]->getRetiring());
        $this->assertSame(300.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $transaction = $transactions[1];
        $this->assertSame(200.00, $transaction->getAmount());
        $transaction->setAmount($transaction->getAmount() - 10);
        $this->assertSame(190.00, $transaction->getAmount());

        $this->backupFactory->create()->compute($this->backupRepository, $transaction->getId());

        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(295.00, $transactions[0]->getRetiring());
        $this->assertSame(295.00, $transactions[0]->getHoliday());
        $this->assertSame(590.00, $transactions[0]->getBalance());
    }
}
