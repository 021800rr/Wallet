<?php

namespace App\Service\BalanceUpdater;

use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use Exception;

interface BalanceUpdaterInterface
{
    /**
     * @param WalletRepository|BackupRepository $transactionRepository
     * @param int $id
     * @throws Exception
     */
    public function compute($transactionRepository, int $id): void;
}
