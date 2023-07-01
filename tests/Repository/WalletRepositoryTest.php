<?php

namespace App\Tests\Repository;

use App\Tests\SetUp;
use Doctrine\ORM\Query\Parameter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletRepositoryTest extends KernelTestCase
{
    use SetUp;

    public function testFindAll(): void
    {
        $walletTransactions = $this->walletRepository->findAll();
        $this->assertSame(3, count($walletTransactions));
    }

    public function testGetAllRecords(): void
    {
        $walletTransactions = $this->walletRepository->getAllRecords();
        $this->assertSame(3, count($walletTransactions));
    }

    public function testSearch(): void
    {
        $paginator = $this->walletRepository->search('-10', 15);

        /** @var Parameter $parameter */
        $parameter = $paginator->getQuery()->getParameters()[2];
        $this->assertSame("amount", $parameter->getName());
        $this->assertSame(-10.0, $parameter->getValue());
        $this->assertSame(1, $paginator->count());

        $paginator = $this->walletRepository->search('191', 15);

        /** @var Parameter $parameter */
        $parameter = $paginator->getQuery()->getParameters()[3];
        $this->assertSame("balance", $parameter->getName());
        $this->assertSame(191.0, $parameter->getValue());
        $this->assertSame(1, $paginator->count());

        $paginator = $this->walletRepository->search('all', 15);

        /** @var Parameter $parameter */
        $parameter = $paginator->getQuery()->getParameters()[0];
        $this->assertSame("contractor", $parameter->getName());
        $this->assertSame("%all%", $parameter->getValue());
        $this->assertSame(2, $paginator->count());
    }
}
