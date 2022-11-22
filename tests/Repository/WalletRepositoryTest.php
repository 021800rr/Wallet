<?php

namespace App\Tests\Repository;

use App\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletRepositoryTest extends KernelTestCase
{
    use SetupTrait;

    public function testFindAll(): void
    {
        $walletTransactions = $this->entityManager
            ->getRepository(Wallet::class)
            ->findAll();
        $this->assertSame(3, count($walletTransactions));
    }
}
