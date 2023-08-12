<?php

namespace App\Tests\Service\Transfer;

use App\Entity\Backup;
use App\Entity\Pln;
use App\Service\BalanceUpdater\BalanceUpdaterBackup;
use App\Service\BalanceUpdater\BalanceUpdaterWallet;
use App\Service\Transfer\Transfer;
use App\Tests\SetUp;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TransferTest extends KernelTestCase
{
    use SetUp;

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function testMoveAssets(): void
    {
        $transfer = new Transfer(
            $this->walletUpdater,
            $this->backupUpdater,
            $this->contractorRepository,
            $this->backupRepository,
            $this->plnRepository,
        );

        $this->assertSame(100.00, $this->plnRepository->getCurrentBalance());

        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(300.00, $transactions[0]->getRetiring());
        $this->assertSame(300.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $backup = new Backup();
        $backup->setContractor($this->internalTransferOwner);
        $backup->setAmount(100);

        $transfer->moveToBackup($backup);

        $this->assertSame(0.00, $this->plnRepository->getCurrentBalance());

        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(350.00, $transactions[0]->getRetiring());
        $this->assertSame(350.00, $transactions[0]->getHoliday());
        $this->assertSame(700.00, $transactions[0]->getBalance());

        $pln = new Pln();
        $pln->setContractor($this->internalTransferOwner);
        $pln->setAmount(100);

        $transfer->moveToPln($pln);

        $this->assertSame(100.00, $this->plnRepository->getCurrentBalance());

        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(350.00, $transactions[0]->getRetiring());
        $this->assertSame(250.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $backup = new Backup();
        $backup->setContractor($this->internalTransferOwner);
        $backup->setAmount(100);

        $transfer->moveToBackup($backup, 1);
        $this->assertSame(0.00, $this->plnRepository->getCurrentBalance());
        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(0.00, $transactions[0]->getRetiring());
        $this->assertSame(0.00, $transactions[0]->getHoliday());
        $this->assertSame(0.00, $transactions[0]->getBalance());
    }
}
