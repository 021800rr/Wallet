<?php

namespace App\Tests\Repository;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletRepositoryTest extends KernelTestCase
{
    use Setup;

    public function testFindAll(): void
    {
        $walletTransactions = $this->entityManager
            ->getRepository(Wallet::class)
            ->findAll();
        $this->assertSame(3, count($walletTransactions));
    }

    public function testGetAllRecords(): void
    {
        $walletRepository = $this->getRepository();
        $walletTransactions = $walletRepository->getAllRecords();
        $this->assertSame(3, count($walletTransactions));
    }

    public function testSearch(): void
    {
        $walletRepository = $this->getRepository();
        $paginator = $walletRepository->search('-10', 15);

        $this->assertSame("amount", $paginator->getQuery()->getParameters()[2]->getName());
        $this->assertSame(-10.0, $paginator->getQuery()->getParameters()[2]->getValue());
        $this->assertSame(1, $paginator->count());

        $paginator = $walletRepository->search('191', 15);

        $this->assertSame("balance", $paginator->getQuery()->getParameters()[3]->getName());
        $this->assertSame(191.0, $paginator->getQuery()->getParameters()[3]->getValue());
        $this->assertSame(1, $paginator->count());

        $paginator = $walletRepository->search('all', 15);

        $this->assertSame("contractor", $paginator->getQuery()->getParameters()[0]->getName());
        $this->assertSame("%all%", $paginator->getQuery()->getParameters()[0]->getValue());
        $this->assertSame(2, $paginator->count());
    }

    private function getRepository(): WalletRepository
    {
        /** @var WalletRepository */
        return $this->entityManager->getRepository(Wallet::class);
    }
}
