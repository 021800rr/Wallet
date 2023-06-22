<?php

namespace App\Tests\Service;

use App\Entity\Chf;
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
        $this->backupRepository = static::getContainer()->get(BackupRepository::class);
        $this->chfRepository = static::getContainer()->get(ChfRepository::class);
        $this->contractorRepository = static::getContainer()->get(ContractorRepository::class);
        $this->feeRepository = static::getContainer()->get(FeeRepository::class);
        $this->walletRepository = static::getContainer()->get(WalletRepository::class);

        $this->backupBalanceUpdater = new BackupBalanceUpdater();
        $this->walletBalanceUpdater = new WalletBalanceUpdater();

        $this->wallets = $this->walletRepository->getAllRecords();
        $this->chfs = $this->chfRepository->getAllRecords();
    }
}
