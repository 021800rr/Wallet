<?php

namespace App\Tests\Repository;

use App\Entity\Backup;
use App\Entity\Chf;
use App\Entity\Eur;
use App\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccountTraitTest extends KernelTestCase
{
    use SetupTrait;

    public function testBackupGetCurrentBalance(): void
    {
        $balance = $this->entityManager
            ->getRepository(Backup::class)
            ->getCurrentBalance();
        $this->assertSame(600.0, $balance);
    }

    public function testBackupGetLastRecord(): void
    {
        $last = $this->entityManager
            ->getRepository(Backup::class)
            ->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(600.0, $last->getBalance());
    }

    public function testChfGetCurrentBalance(): void
    {
        $balance = $this->entityManager
            ->getRepository(Chf::class)
            ->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    public function testChfGetLastRecord(): void
    {
        $last = $this->entityManager
            ->getRepository(Chf::class)
            ->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    public function testEurGetCurrentBalance(): void
    {
        $balance = $this->entityManager
            ->getRepository(Eur::class)
            ->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    public function testEurGetLastRecord(): void
    {
        $last = $this->entityManager
            ->getRepository(Eur::class)
            ->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    public function testWalletGetCurrentBalance(): void
    {
        $balance = $this->entityManager
            ->getRepository(Wallet::class)
            ->getCurrentBalance();
        $this->assertSame(170.0, $balance);
    }

    public function testWalletGetLastRecord(): void
    {
        $last = $this->entityManager
            ->getRepository(Wallet::class)
            ->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(170.0, $last->getBalance());
    }
}
