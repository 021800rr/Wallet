<?php

namespace App\Tests;

use App\Entity\Chf;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\ContractorRepository;
use App\Repository\EurRepository;
use App\Repository\FeeRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\BalanceUpdaterBackup;
use App\Service\BalanceUpdater\BalanceUpdaterBackupFactory;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterWallet;
use App\Service\BalanceUpdater\BalanceUpdaterWalletFactory;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

trait SetupRepos
{
    private BackupRepository $backupRepository;
    private ChfRepository $chfRepository;

    private EurRepository $eurRepository;
    private ContractorRepository $contractorRepository;
    private FeeRepository $feeRepository;
    private WalletRepository $walletRepository;
    private BalanceUpdaterFactoryInterface $backupFactory;
    private BalanceUpdaterFactoryInterface $walletFactory;

    /** @var Wallet[] $wallets */
    private array $wallets;

    /** @var Chf[] $chfs */
    private array $chfs;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function setUpRepos(): void
    {
        /** @var BackupRepository $backupRepository */
        $backupRepository = static::getContainer()->get(BackupRepository::class);
        $this->backupRepository = $backupRepository;

        /** @var ChfRepository $chfRepository */
        $chfRepository = static::getContainer()->get(ChfRepository::class);
        $this->chfRepository = $chfRepository;

        /** @var EurRepository $eurRepository */
        $eurRepository = static::getContainer()->get(EurRepository::class);
        $this->eurRepository = $eurRepository;

        /** @var ContractorRepository $contractorRepository */
        $contractorRepository = static::getContainer()->get(ContractorRepository::class);
        $this->contractorRepository = $contractorRepository;

        /** @var FeeRepository $feeRepository */
        $feeRepository = static::getContainer()->get(FeeRepository::class);
        $this->feeRepository = $feeRepository;

        /** @var WalletRepository $walletRepository */
        $walletRepository = static::getContainer()->get(WalletRepository::class);
        $this->walletRepository = $walletRepository;

        /** @var Wallet[] $wallets */
        $wallets = $this->walletRepository->getAllRecords();
        $this->wallets = $wallets;

        /** @var Chf[] $chfs */
        $chfs = $this->chfRepository->getAllRecords();
        $this->chfs = $chfs;

        $this->backupFactory = new BalanceUpdaterBackupFactory(new BalanceUpdaterBackup());
        $this->walletFactory = new BalanceUpdaterWalletFactory(new BalanceUpdaterWallet());
    }
}
