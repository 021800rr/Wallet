<?php

namespace App\Tests\Service;

use App\Entity\Backup;
use App\Entity\Chf;
use App\Entity\Contractor;
use App\Entity\Fee;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\ContractorRepository;
use App\Repository\FeeRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\BackupBalanceUpdater;
use App\Service\BalanceUpdater\WalletBalanceUpdater;

trait SetUp
{
    private $entityManager;

    private BackupRepository $backupRepository;
    private ChfRepository $chfRepository;
    private ContractorRepository $contractorRepository;
    private FeeRepository $feeRepository;
    private WalletRepository $walletRepository;

    private BackupBalanceUpdater $backupBalanceUpdater;
    private WalletBalanceUpdater $walletBalanceUpdater;

    /** @var Wallet[] $wallets */
    private array $wallets;

    /** @var Chf[] $chfs */
    private array $chfs;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->backupRepository = $this->entityManager->getRepository(Backup::class);
        $this->chfRepository = $this->entityManager->getRepository(Chf::class);
        $this->contractorRepository = $this->entityManager->getRepository(Contractor::class);
        $this->feeRepository = $this->entityManager->getRepository(Fee::class);
        $this->walletRepository = $this->entityManager->getRepository(Wallet::class);

        $this->backupBalanceUpdater = new BackupBalanceUpdater($this->entityManager);
        $this->walletBalanceUpdater = new WalletBalanceUpdater($this->entityManager);

        $this->wallets = $this->walletRepository->getAllRecords();
        $this->chfs = $this->chfRepository->getAllRecords();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
