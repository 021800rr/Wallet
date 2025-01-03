<?php

namespace App\Tests\Repository;

use App\Entity\Backup;
use App\Entity\Chf;
use App\Entity\Eur;
use App\Entity\Pln;
use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccountTraitTest extends KernelTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testBackupGetCurrentBalance(): void
    {
        $balance = $this->backupRepository->getCurrentBalance();
        $this->assertSame(600.0, $balance);
    }

    public function testBackupGetLastRecord(): void
    {
        /** @var Backup $last */
        $last = $this->backupRepository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(600.0, $last->getBalance());
    }

    public function testChfGetCurrentBalance(): void
    {
        $balance = $this->chfRepository->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    public function testChfGetLastRecord(): void
    {
        /** @var Chf $last */
        $last = $this->chfRepository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    public function testEurGetCurrentBalance(): void
    {
        $balance = $this->eurRepository->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    public function testEurGetLastRecord(): void
    {
        /** @var Eur $last */
        $last = $this->eurRepository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    public function testPlnGetCurrentBalance(): void
    {
        $balance = $this->plnRepository->getCurrentBalance();
        $this->assertSame(100.0, $balance);
    }

    public function testPlnGetLastRecord(): void
    {
        /** @var Pln $last */
        $last = $this->plnRepository->getLastRecord();
        $this->assertSame(5, $last->getId());
        $this->assertSame(100.0, $last->getBalance());
    }
}
