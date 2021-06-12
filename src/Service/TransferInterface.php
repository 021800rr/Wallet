<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Wallet;
use App\Repository\ContractorRepository;

interface TransferInterface
{
    public function moveToBackup(Backup $backup): void;

    public function moveToWallet(Wallet $wallet): void;
}
