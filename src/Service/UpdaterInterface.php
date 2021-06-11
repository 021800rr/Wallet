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
    function compute($transactionRepository): void;
}