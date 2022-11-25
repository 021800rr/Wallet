<?php

namespace App\Tests\Service;

use App\Entity\Fee;
use App\Entity\Wallet;
use App\Repository\FeeRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\WalletBalanceUpdater;

trait SetUp
{
    protected $entityManager;

    protected FeeRepository $feeRepository;
    protected WalletRepository $walletRepository;

    protected WalletBalanceUpdater $walletBalanceUpdater;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->feeRepository = $this->entityManager->getRepository(Fee::class);
        $this->walletRepository = $this->entityManager->getRepository(Wallet::class);

        $this->walletBalanceUpdater = new WalletBalanceUpdater($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
