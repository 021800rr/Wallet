<?php

namespace App\Tests\Repository;

use App\Entity\Backup;
use App\Entity\Chf;
use App\Entity\Eur;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\EurRepository;
use App\Repository\WalletRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccountTraitTest extends KernelTestCase
{
    use Setup;

    public function testBackupGetCurrentBalance(): void
    {
        $repository = $this->getBackupRepository();
        $balance = $repository->getCurrentBalance();
        $this->assertSame(600.0, $balance);
    }

    public function testBackupGetLastRecord(): void
    {
        $repository = $this->getBackupRepository();
        $last = $repository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(600.0, $last->getBalance());
    }

    public function testChfGetCurrentBalance(): void
    {
        $repository = $this->getChfRepository();
        $balance = $repository->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    public function testChfGetLastRecord(): void
    {
        $repository = $this->getChfRepository();
        $last = $repository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    public function testEurGetCurrentBalance(): void
    {
        $repository = $this->getEurRepository();
        $balance = $repository->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    public function testEurGetLastRecord(): void
    {
        $repository = $this->getEurRepository();
        $last = $repository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    public function testWalletGetCurrentBalance(): void
    {
        $repository = $this->getWalletRepository();
        $balance = $repository->getCurrentBalance();
        $this->assertSame(170.0, $balance);
    }

    public function testWalletGetLastRecord(): void
    {
        $repository = $this->getWalletRepository();
        $last = $repository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(170.0, $last->getBalance());
    }

    private function getBackupRepository(): BackupRepository
    {
        /** @var BackupRepository */
        return $this->entityManager->getRepository(Backup::class);
    }

    private function getChfRepository(): ChfRepository
    {
        /** @var ChfRepository */
        return $this->entityManager->getRepository(Chf::class);
    }

    private function getEurRepository(): EurRepository
    {
        /** @var EurRepository */
        return $this->entityManager->getRepository(Eur::class);
    }

    private function getWalletRepository(): WalletRepository
    {
        /** @var WalletRepository */
        return $this->entityManager->getRepository(Wallet::class);
    }
}
