<?php

namespace App\Service\Transfer;

use App\Entity\AbstractAccount;
use App\Entity\Backup;
use App\Entity\Wallet;
use App\Repository\AccountRepositoryInterface;
use App\Repository\BackupRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Exception;

class Transfer implements TransferInterface
{
    public function __construct(
        private readonly ContractorRepositoryInterface $contractorRepository,
        private readonly BalanceUpdaterInterface $backupUpdater,
        private readonly BackupRepositoryInterface $backupRepository,
        private readonly BalanceUpdaterInterface $walletUpdater,
        private readonly WalletRepositoryInterface $walletRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function moveToBackup(Backup $backup, int $currency = 0): void
    {
        $backup = $this->persistExport($this->backupRepository, $backup);
        $wallet = $this->persistImport($this->walletRepository, new Wallet(), $backup);
        if (0 === $currency) {
            $this->backupUpdater->compute($this->backupRepository, $backup->getId());
        }
        $this->walletUpdater->compute($this->walletRepository, $wallet->getId());
    }

    /**
     * @throws Exception
     */
    public function moveToWallet(Wallet $wallet): void
    {
        $wallet = $this->persistExport($this->walletRepository, $wallet);
        $backup = $this->persistImport($this->backupRepository, new Backup(), $wallet);

        $this->walletUpdater->compute($this->walletRepository, $wallet->getId());
        $this->backupUpdater->compute($this->backupRepository, $backup->getId());
    }

    private function persistImport(
        AccountRepositoryInterface $repository,
        AbstractAccount $fromAccount,
        AbstractAccount $toAccount
    ): AbstractAccount {
        $contractor = $this->contractorRepository->getInternalTransferOwner();
        $fromAccount->setContractor($contractor);
        $fromAccount->setAmount(-1 * $toAccount->getAmount());
        $repository->save($fromAccount, true);

        return $fromAccount;
    }

    private function persistExport(AccountRepositoryInterface $repository, AbstractAccount $toAccount): AbstractAccount
    {
        $contractor = $this->contractorRepository->getInternalTransferOwner();
        $toAccount->setContractor($contractor);
        $repository->save($toAccount, true);

        return $toAccount;
    }
}
