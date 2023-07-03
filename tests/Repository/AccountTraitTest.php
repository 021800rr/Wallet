<?php

namespace App\Tests\Repository;

use App\Entity\Backup;
use App\Entity\Chf;
use App\Entity\Eur;
use App\Entity\Pln;
use App\Tests\SetUp;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccountTraitTest extends KernelTestCase
{
    use SetUp;

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function testBackupGetCurrentBalance(): void
    {
        $balance = $this->backupRepository->getCurrentBalance();
        $this->assertSame(600.0, $balance);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function testBackupGetLastRecord(): void
    {
        /** @var Backup $last */
        $last = $this->backupRepository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(600.0, $last->getBalance());
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function testChfGetCurrentBalance(): void
    {
        $balance = $this->chfRepository->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function testChfGetLastRecord(): void
    {
        /** @var Chf $last */
        $last = $this->chfRepository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function testEurGetCurrentBalance(): void
    {
        $balance = $this->eurRepository->getCurrentBalance();
        $this->assertSame(70.07, $balance);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function testEurGetLastRecord(): void
    {
        /** @var Eur $last */
        $last = $this->eurRepository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(70.07, $last->getBalance());
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function testPlnGetCurrentBalance(): void
    {
        $balance = $this->plnRepository->getCurrentBalance();
        $this->assertSame(170.0, $balance);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testPlnGetLastRecord(): void
    {
        /** @var Pln $last */
        $last = $this->plnRepository->getLastRecord();
        $this->assertSame(3, $last->getId());
        $this->assertSame(170.0, $last->getBalance());
    }
}
