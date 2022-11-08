<?php

namespace App\Service\Transfer;

use App\Entity\AbstractAccount;
use App\Entity\Backup;
use App\Entity\Wallet;
use App\Repository\BackupRepositoryInterface;
use App\Repository\ContractorRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Transfer implements TransferInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
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
        $backup = $this->persistExport($backup);
        $wallet = $this->persistImport(new Wallet(), $backup);
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
        $wallet = $this->persistExport($wallet);
        $backup = $this->persistImport(new Backup(), $wallet);

        $this->walletUpdater->compute($this->walletRepository, $wallet->getId());
        $this->backupUpdater->compute($this->backupRepository, $backup->getId());
    }

    private function persistImport(AbstractAccount $fromAccount, AbstractAccount $toAccount): AbstractAccount
    {
        $contractor = $this->contractorRepository->getInternalTransferOwner();
        $fromAccount->setContractor($contractor);
        $fromAccount->setAmount(-1 * $toAccount->getAmount());
        $this->entityManager->persist($fromAccount);
        $this->entityManager->flush();

        return $fromAccount;
    }

    private function persistExport(AbstractAccount $toAccount): AbstractAccount
    {
        $contractor = $this->contractorRepository->getInternalTransferOwner();
        $toAccount->setContractor($contractor);
        $this->entityManager->persist($toAccount);
        $this->entityManager->flush();

        return $toAccount;
    }
}
