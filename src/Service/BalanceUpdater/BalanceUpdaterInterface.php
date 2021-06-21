<?php

namespace App\Service\BalanceUpdater;

use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use Exception;

interface BalanceUpdaterInterface
{
    /**
     * @param WalletRepository|BackupRepository $transactionRepository
     * @throws Exception
     */
    public function compute($transactionRepository): void;
}
