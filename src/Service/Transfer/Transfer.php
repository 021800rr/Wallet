<?php

namespace App\Service\Transfer;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\ContractorRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class Transfer implements TransferInterface
{
    private EntityManagerInterface $entityManager;
    private ContractorRepository $contractorRepository;
    private BalanceUpdaterInterface $backupUpdater;
    private BackupRepository $backupRepository;
    private BalanceUpdaterInterface $walletUpdater;
    private WalletRepository $walletRepository;

    public function __construct(
        EntityManagerInterface $entityManage,
        ContractorRepository $contractorRepository,
        BalanceUpdaterInterface $backupUpdater,
        BackupRepository $backupRepository,
        BalanceUpdaterInterface $walletUpdater,
        WalletRepository $walletRepository
    ) {
        $this->entityManager = $entityManage;
        $this->contractorRepository = $contractorRepository;
        $this->backupUpdater = $backupUpdater;
        $this->backupRepository = $backupRepository;
        $this->walletUpdater = $walletUpdater;
        $this->walletRepository = $walletRepository;
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

    private function persistImport(Wallet|Backup $fromAccount, Backup|Wallet $toAccount): Backup|Wallet
    {
        $contractor = $this->contractorRepository->getInternalTransferOwner();
        $fromAccount->setContractor($contractor);
        $fromAccount->setAmount(-1 * $toAccount->getAmount());
        $this->entityManager->persist($fromAccount);
        $this->entityManager->flush();

        return $fromAccount;
    }

    private function persistExport(Backup|Wallet $toAccount): Backup|Wallet
    {
        $contractor = $this->contractorRepository->getInternalTransferOwner();
        $toAccount->setContractor($contractor);
        $this->entityManager->persist($toAccount);
        $this->entityManager->flush();

        return $toAccount;
    }
}
