<?php

namespace App\Tests\Service;

use App\Entity\Wallet;
use App\Service\BalanceUpdater\WalletBalanceUpdater;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletBalanceUpdaterTest extends KernelTestCase
{
    private $entityManager;

    /**
     * @throws Exception
     */
    public function testCompute(): void
    {
        $walletRepository = $this->entityManager->getRepository(Wallet::class);
        $walletBalanceUpdater = new WalletBalanceUpdater($this->entityManager);

        $transactions = $walletRepository->findAll();
        $oldBalance = ($transactions[0])->getBalance();
        $this->assertSame(170.00, $oldBalance);
        /** @var Wallet $transaction */
        $transaction = $transactions[1];
        $this->assertSame(-10.00, $transaction->getAmount());
        $transaction->setAmount($transaction->getAmount() - 10);
        $this->assertSame(-20.00, $transaction->getAmount());

        $walletBalanceUpdater->compute($walletRepository, $transaction->getId());

        $transactions = $walletRepository->findAll();
        $newBalance = ($transactions[0])->getBalance();

        $this->assertSame(160.00, $newBalance);
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
