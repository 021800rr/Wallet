<?php

namespace App\Service\BalanceUpdater;

use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\EurRepository;
use App\Repository\WalletRepository;
use Exception;

interface BalanceUpdaterInterface
{
    /**
     * @param WalletRepository|ChfRepository|EurRepository|BackupRepository $transactionRepository
     * @param int $id
     * @throws Exception
     */
    public function compute($transactionRepository, int $id): void;
}
