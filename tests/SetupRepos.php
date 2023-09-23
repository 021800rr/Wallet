<?php

namespace App\Tests;

use App\Entity\Chf;
use App\Entity\Contractor;
use App\Entity\Pln;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\ContractorRepository;
use App\Repository\EurRepository;
use App\Repository\FeeRepository;
use App\Repository\PlnRepository;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use App\Service\BalanceUpdater\BalanceUpdaterBackup;
use App\Service\BalanceUpdater\BalanceUpdaterWallet;
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
    private EurRepository $eurRepository;

    private ContractorRepository $contractorRepository;
    private FeeRepository $feeRepository;
    private PlnRepository $plnRepository;

    /** @var Pln[] $plns */
    private array $plns;

    /** @var Chf[] $chfs */
    private array $chfs;

    private Contractor $internalTransferOwner;

    private BalanceUpdaterAccountInterface $walletUpdater;
    private BalanceUpdaterAccountInterface $backupUpdater;

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

        /** @var EurRepository $eurRepository */
        $eurRepository = static::getContainer()->get(EurRepository::class);
        $this->eurRepository = $eurRepository;

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

        $this->internalTransferOwner = $contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');

        $this->backupUpdater = new BalanceUpdaterBackup();
        $this->walletUpdater = new BalanceUpdaterWallet();
    }
}
