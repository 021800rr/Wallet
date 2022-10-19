<?php

namespace App\Service;

use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use Exception;

interface UpdaterInterface
{
    /**
     * @param WalletRepository|BackupRepository $transactionRepository
     * @throws Exception
     */
    public function compute(WalletRepository|BackupRepository $transactionRepository): void;
}
