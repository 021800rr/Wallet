<?php

namespace App\Tests\Service\Transfer;

use App\Entity\Backup;
use App\Entity\Wallet;
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
        $contractor = $this->contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
        $transfer = new Transfer(
            $this->contractorRepository,
            $this->backupFactory,
            $this->backupRepository,
            $this->walletFactory,
            $this->walletRepository,
        );

        $this->assertSame(170.00, $this->walletRepository->getCurrentBalance());

        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(300.00, $transactions[0]->getRetiring());
        $this->assertSame(300.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $backup = new Backup();
        $backup->setContractor($contractor);
        $backup->setAmount(100);

        $transfer->moveToBackup($backup);

        $this->assertSame(70.00, $this->walletRepository->getCurrentBalance());

        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(350.00, $transactions[0]->getRetiring());
        $this->assertSame(350.00, $transactions[0]->getHoliday());
        $this->assertSame(700.00, $transactions[0]->getBalance());

        $wallet = new Wallet();
        $wallet->setContractor($contractor);
        $wallet->setAmount(100);

        $transfer->moveToWallet($wallet);

        $this->assertSame(170.00, $this->walletRepository->getCurrentBalance());

        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(350.00, $transactions[0]->getRetiring());
        $this->assertSame(250.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $backup = new Backup();
        $backup->setContractor($contractor);
        $backup->setAmount(100);

        $transfer->moveToBackup($backup, 1);
        $this->assertSame(70.00, $this->walletRepository->getCurrentBalance());
        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(0.00, $transactions[0]->getRetiring());
        $this->assertSame(0.00, $transactions[0]->getHoliday());
        $this->assertSame(0.00, $transactions[0]->getBalance());
    }
}
