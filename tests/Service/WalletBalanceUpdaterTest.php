<?php

namespace App\Service\Tests;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use App\Service\WalletBalanceUpdater;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletBalanceUpdaterTest extends KernelTestCase
{
    /** @var Doctrine\ORM\EntityManager */
    private $entityManager;
    private WalletBalanceUpdater $walletBalanceUpdater;
    private WalletRepository $walletRepository;

    public function testCompute(): void
    {
        $transactions = $this->walletRepository->findAll();
        $oldBalance = ($transactions[0])->getBalance();
        $this->assertSame(170.00, $oldBalance);
        /** @var Wallet $transaction */
        $transaction = $transactions[1];
        $this->assertSame(-10.00, $transaction->getAmount());
        $transaction->setAmount($transaction->getAmount() - 10);
        $this->assertSame(-20.00, $transaction->getAmount());

        $this->walletBalanceUpdater->compute($this->walletRepository);

        $transactions = $this->walletRepository->findAll();
        $newBalance = ($transactions[0])->getBalance();

        $this->assertSame(160.00, $newBalance);
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->walletRepository = $this->entityManager->getRepository(Wallet::class);

        $this->walletBalanceUpdater = new WalletBalanceUpdater(
            $this->entityManager,
            $this->walletRepository,
            $this->entityManager->getRepository(Backup::class),
            $this->entityManager->getRepository(Contractor::class)
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
