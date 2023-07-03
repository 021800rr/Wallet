<?php

namespace App\Tests;

use App\Entity\Chf;
use App\Entity\Contractor;
use App\Entity\Pln;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\ContractorRepository;
use App\Repository\FeeRepository;
use App\Repository\PlnRepository;
use App\Service\BalanceUpdater\BalanceUpdaterBackup;
use App\Service\BalanceUpdater\BalanceUpdaterBackupFactory;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterWallet;
use App\Service\BalanceUpdater\BalanceUpdaterWalletFactory;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

trait SetupRepos
{
    private BackupRepository $backupRepository;
    private ChfRepository $chfRepository;

    private ContractorRepository $contractorRepository;
    private FeeRepository $feeRepository;
    private PlnRepository $plnRepository;
    private BalanceUpdaterFactoryInterface $backupFactory;
    private BalanceUpdaterFactoryInterface $walletFactory;

    /** @var Pln[] $plns */
    private array $plns;

    /** @var Chf[] $chfs */
    private array $chfs;

    private Contractor $internalTransferOwner;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    protected function setUpRepos(): void
    {
        /** @var BackupRepository $backupRepository */
        $backupRepository = static::getContainer()->get(BackupRepository::class);
        $this->backupRepository = $backupRepository;

        /** @var ChfRepository $chfRepository */
        $chfRepository = static::getContainer()->get(ChfRepository::class);
        $this->chfRepository = $chfRepository;

        /** @var ContractorRepository $contractorRepository */
        $contractorRepository = static::getContainer()->get(ContractorRepository::class);
        $this->contractorRepository = $contractorRepository;

        /** @var FeeRepository $feeRepository */
        $feeRepository = static::getContainer()->get(FeeRepository::class);
        $this->feeRepository = $feeRepository;

        /** @var PlnRepository $plnRepository */
        $plnRepository = static::getContainer()->get(PlnRepository::class);
        $this->plnRepository = $plnRepository;

        /** @var Pln[] $plns */
        $plns = $this->plnRepository->getAllRecords();
        $this->plns = $plns;

        /** @var Chf[] $chfs */
        $chfs = $this->chfRepository->getAllRecords();
        $this->chfs = $chfs;

        $this->backupFactory = new BalanceUpdaterBackupFactory(new BalanceUpdaterBackup());
        $this->walletFactory = new BalanceUpdaterWalletFactory(new BalanceUpdaterWallet());

        $this->internalTransferOwner = $contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
    }
}
