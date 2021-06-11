<?php

namespace App\Tests\Service;

use App\Entity\Backup;
use App\Service\BackupBalanceUpdater;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BackupBalanceUpdaterTest extends KernelTestCase
{
    private $entityManager;

    public function testCompute(): void
    {
        $backupBalanceUpdater = new BackupBalanceUpdater($this->entityManager);
        $backupRepository = $this->entityManager->getRepository(Backup::class);

        /** @var Backup[] $transactions */
        $transactions = $backupRepository->findAll();
        $this->assertSame(300.00, ($transactions[0])->getRetiring());
        $this->assertSame(300.00, ($transactions[0])->getHoliday());
        $this->assertSame(600.00, ($transactions[0])->getBalance());

        $transaction = $transactions[1];
        $this->assertSame(200.00, $transaction->getAmount());
        $transaction->setAmount($transaction->getAmount() - 10);
        $this->assertSame(190.00, $transaction->getAmount());

        $backupBalanceUpdater->compute($backupRepository);

        $transactions = $backupRepository->findAll();
        $this->assertSame(295.00, ($transactions[0])->getRetiring());
        $this->assertSame(295.00, ($transactions[0])->getHoliday());
        $this->assertSame(590.00, ($transactions[0])->getBalance());
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
