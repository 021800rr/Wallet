<?php

namespace App\Tests\Repository;

use App\Entity\Wallet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletRepositoryTest extends KernelTestCase
{
    /** @var Doctrine\ORM\EntityManager */
    private $entityManager;

    public function testFindAll(): void
    {
        $walletTransactions = $this->entityManager
            ->getRepository(Wallet::class)
            ->findAll();
        $this->assertSame(3, count($walletTransactions));
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
