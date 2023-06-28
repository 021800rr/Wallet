<?php

namespace App\Service\Transfer;

use App\Entity\AbstractAccount;
use App\Entity\Backup;
use App\Entity\Wallet;
use App\Repository\AccountRepositoryInterface;
use App\Repository\BackupRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;

readonly class Transfer implements TransferInterface
{
    public function __construct(
        private ContractorRepositoryInterface  $contractorRepository,
        private BalanceUpdaterFactoryInterface $backupFactory,
        private BackupRepositoryInterface      $backupRepository,
        private BalanceUpdaterFactoryInterface $walletFactory,
        private WalletRepositoryInterface      $walletRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function moveToBackup(Backup $backup, int $currency = 0): void
    {
        $this->backupRepository->save($backup, true);
        $wallet = $this->persistDebit($this->walletRepository, new Wallet(), $backup);
        if (0 === $currency) {
            $this->backupFactory->create()->compute($this->backupRepository, $backup->getId());
        }
        $this->walletFactory->create()->compute($this->walletRepository, $wallet->getId());
    }

    /**
     * @throws Exception
     */
    public function moveToWallet(Wallet $wallet): void
    {
        $this->walletRepository->save($wallet, true);
        $backup = $this->persistDebit($this->backupRepository, new Backup(), $wallet);

        $this->walletFactory->create()->compute($this->walletRepository, $wallet->getId());
        $this->backupFactory->create()->compute($this->backupRepository, $backup->getId());
    }

    private function persistDebit(
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
}
