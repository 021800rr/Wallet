<?php

namespace App\Service\Transfer;

use App\Entity\Backup;
use App\Entity\Wallet;

interface TransferInterface
{
    public function moveToBackup(Backup $backup, int $currency = 0): void;

    public function moveToWallet(Wallet $wallet): void;
}
