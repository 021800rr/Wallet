<?php

namespace App\Tests\Service\Transfer;

use App\Entity\Backup;
use App\Entity\Wallet;
use App\Service\Transfer\Transfer;
use App\Tests\Service\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransferTest extends KernelTestCase
{
    use SetUp;

    public function testMoveAssets(): void
    {
        $transfer = new Transfer(
            $this->entityManager,
            $this->contractorRepository,
            $this->backupBalanceUpdater,
            $this->backupRepository,
            $this->walletBalanceUpdater,
            $this->walletRepository
        );

        $this->assertSame(170.00, $this->walletRepository->getCurrentBalance());

        $transactions = $this->backupRepository->findAll();
        $this->assertSame(300.00, $transactions[0]->getRetiring());
        $this->assertSame(300.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $backup = new Backup();
        $backup->setAmount(100);

        $transfer->moveToBackup($backup);

        $this->assertSame(70.00, $this->walletRepository->getCurrentBalance());

        $transactions = $this->backupRepository->findAll();
        $this->assertSame(350.00, $transactions[0]->getRetiring());
        $this->assertSame(350.00, $transactions[0]->getHoliday());
        $this->assertSame(700.00, $transactions[0]->getBalance());

        $wallet = new Wallet();
        $wallet->setAmount(100);

        $transfer->moveToWallet($wallet);

        $this->assertSame(170.00, $this->walletRepository->getCurrentBalance());

        $transactions = $this->backupRepository->findAll();
        $this->assertSame(350.00, $transactions[0]->getRetiring());
        $this->assertSame(250.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());
    }
}
