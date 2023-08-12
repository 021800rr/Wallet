<?php

namespace App\Service\Transfer;

use App\Entity\AbstractAccount;
use App\Entity\Backup;
use App\Entity\Pln;
use App\Repository\AccountRepositoryInterface;
use App\Repository\BackupRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;

readonly class Transfer implements TransferInterface
{
    public function __construct(
        private BalanceUpdaterAccountInterface $walletUpdater,
        private BalanceUpdaterAccountInterface $backupUpdater,
        private ContractorRepositoryInterface  $contractorRepository,
        private BackupRepositoryInterface      $backupRepository,
        private AccountRepositoryInterface     $plnRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function moveToBackup(Backup $backup, int $currency = 0): void
    {
        $this->backupRepository->save($backup, true);
        $this->backupUpdater->setPreviousId($this->backupRepository, $backup->getId());
        $pln = $this->persistDebit($this->plnRepository, new Pln(), $backup);
        $this->walletUpdater->setPreviousId($this->plnRepository, $pln->getId());
        if (0 === $currency) {
            $this->backupUpdater->compute($this->backupRepository, $backup->getId());
        }
        $this->walletUpdater->compute($this->plnRepository, $pln->getId());
    }

    /**
     * @throws Exception
     */
    public function moveToPln(Pln $pln): void
    {
        $this->plnRepository->save($pln, true);
        $this->walletUpdater->setPreviousId($this->plnRepository, $pln->getId());
        $backup = $this->persistDebit($this->backupRepository, new Backup(), $pln);
        $this->backupUpdater->setPreviousId($this->backupRepository, $backup->getId());
        $this->walletUpdater->compute($this->plnRepository, $pln->getId());
        $this->backupUpdater->compute($this->backupRepository, $backup->getId());
    }

    /**
     * @throws Exception
     */
    private function persistDebit(
        AccountRepositoryInterface $repository,
        AbstractAccount            $fromAccount,
        AbstractAccount            $toAccount,
    ): AbstractAccount {
        $internalTransferOwner = $this->contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
        $fromAccount->setContractor($internalTransferOwner);
        $fromAccount->setAmount(-1 * $toAccount->getAmount());
        $repository->save($fromAccount, true);

        return $fromAccount;
    }
}
