<?php

namespace App\Tests\Service;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Entity\Wallet;
use App\Service\BackupBalanceUpdater;
use App\Service\Transfer;
use App\Service\WalletBalanceUpdater;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransferTest extends KernelTestCase
{
    private $entityManager;
    private Transfer $transfer;

    public function testMoveAssets(): void
    {
        $walletRepository = $this->entityManager->getRepository(Wallet::class);
        $backupRepository = $this->entityManager->getRepository(Backup::class);

        $this->assertSame(170.00, $walletRepository->getCurrentBalance());

        $transactions = $backupRepository->findAll();
        $this->assertSame(300.00, ($transactions[0])->getRetiring());
        $this->assertSame(300.00, ($transactions[0])->getHoliday());
        $this->assertSame(600.00, ($transactions[0])->getBalance());

        $backup = new Backup();
        $backup->setAmount(100);

        $this->transfer->moveToBackup($backup);

        $this->assertSame(70.00, $walletRepository->getCurrentBalance());

        $transactions = $backupRepository->findAll();
        $this->assertSame(350.00, ($transactions[0])->getRetiring());
        $this->assertSame(350.00, ($transactions[0])->getHoliday());
        $this->assertSame(700.00, ($transactions[0])->getBalance());

        $wallet = new Wallet();
        $wallet->setAmount(100);

        $this->transfer->moveToWallet($wallet);

        $this->assertSame(170.00, $walletRepository->getCurrentBalance());

        $transactions = $backupRepository->findAll();
        $this->assertSame(350.00, ($transactions[0])->getRetiring());
        $this->assertSame(250.00, ($transactions[0])->getHoliday());
        $this->assertSame(600.00, ($transactions[0])->getBalance());
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $contractorRepository = $this->entityManager->getRepository(Contractor::class);
        $backupBalanceUpdater = new BackupBalanceUpdater($this->entityManager);
        $backupRepository = $this->entityManager->getRepository(Backup::class);
        $walletBalanceUpdate = new WalletBalanceUpdater($this->entityManager);
        $walletRepository = $this->entityManager->getRepository(Wallet::class);

        $this->transfer = new Transfer(
            $this->entityManager,
            $contractorRepository,
            $backupBalanceUpdater,
            $backupRepository,
            $walletBalanceUpdate,
            $walletRepository
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
