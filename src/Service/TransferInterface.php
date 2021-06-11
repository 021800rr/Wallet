<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Wallet;
use App\Repository\ContractorRepository;

interface TransferInterface
{
    function moveToBackup(Backup $backup): void;

    function moveToWallet(Wallet $wallet): void;
}
