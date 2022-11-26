<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Backup;
use App\Entity\PaymentsByMonth;
use App\Repository\AccountRepositoryInterface;
use App\Repository\BackupRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\ExpectedBackup\CalculatorInterface;

class PaymentByMonthProvider implements ProviderInterface
{
    public function __construct(
        private readonly BackupRepositoryInterface    $backupRepository,
        private readonly CalculatorInterface          $calculator,
        private readonly WalletRepositoryInterface    $walletRepository,
        private readonly AccountRepositoryInterface   $chfRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /**
         * @var array $backups
         * [
         *      [
         *          yearMonth => 2021-06,
         *          sum_of_amount => 300
         *      ],
         *      [
         *          yearMonth => 2021-05,
         *          sum_of_amount => 100
         *      ]
         * ]
         */
        $backups = $this->backupRepository->paymentsByMonth();

        /** @var Backup[] $backupLastRecords */
        $backupLastRecord = $this->backupRepository->getLastRecord();

        $walletBalance = $this->walletRepository->getCurrentBalance();
        
        $paymentByMonth = new PaymentsByMonth();

        $paymentByMonth->setBackups($backups);
        $paymentByMonth->setExpected($this->calculator->compute($backups));
        $paymentByMonth->setWalletBalance($walletBalance);
        $paymentByMonth->setChfBalance($this->chfRepository->getCurrentBalance());
        $paymentByMonth->setBackupLastRecord($backupLastRecord);
        $paymentByMonth->setTotal($walletBalance + $backupLastRecord->getBalance());

        return $paymentByMonth;
    }
}
