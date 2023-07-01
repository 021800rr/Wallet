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

readonly class PaymentByMonthProvider implements ProviderInterface
{
    public function __construct(
        private BackupRepositoryInterface  $backupRepository,
        private CalculatorInterface        $calculator,
        private WalletRepositoryInterface  $walletRepository,
        private AccountRepositoryInterface $chfRepository,
    ) {
    }

    /**
     * @param array<mixed, mixed> $uriVariables
     * @param array<mixed, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // array $backups
        // [
        //      [
        //          yearMonth => 2021-06,
        //          sum_of_amount => 300
        //      ],
        //      [
        //          yearMonth => 2021-05,
        //          sum_of_amount => 100
        //      ]
        // ]
        $backups = $this->backupRepository->paymentsByMonth();

        /** @var Backup $backupLastRecord */
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
