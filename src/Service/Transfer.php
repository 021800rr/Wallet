<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\ContractorRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;

class Transfer implements TransferInterface
{
    private EntityManagerInterface $entityManager;
    private ContractorRepository $contractorRepository;
    private UpdaterInterface $backupUpdater;
    private BackupRepository $backupRepository;
    private UpdaterInterface $updater;
    private WalletRepository $walletRepository;

    public function __construct(
        EntityManagerInterface $entityManage,
        ContractorRepository $contractorRepository,
        UpdaterInterface $backupUpdater,
        BackupRepository $backupRepository,
        UpdaterInterface $updater,
        WalletRepository $walletRepository
    )
    {
        $this->entityManager = $entityManage;
        $this->contractorRepository = $contractorRepository;
        $this->backupUpdater = $backupUpdater;
        $this->backupRepository = $backupRepository;
        $this->updater = $updater;
        $this->walletRepository = $walletRepository;
    }

    public function moveToBackup(Backup $backup): void
    {
        $this->persistExport($backup);
        $this->persistImport(new Wallet(), $backup);

        $this->backupUpdater->compute($this->backupRepository);
        $this->updater->compute($this->walletRepository);
    }

    public function moveToWallet(Wallet $wallet): void
    {
        $this->persistExport($wallet);
        $this->persistImport(new Backup(), $wallet);

        $this->updater->compute($this->walletRepository);
        $this->backupUpdater->compute($this->backupRepository);
    }

    /**
     * @param Wallet|Backup $fromAccount
     * @param Backup|Wallet $toAccount
     */
    private function persistImport($fromAccount, $toAccount): void
    {
        $contractor = $this->getContractor();
        $fromAccount->setContractor($contractor);
        $fromAccount->setAmount(-1 * $toAccount->getAmount());
        $this->entityManager->persist($fromAccount);
        $this->entityManager->flush();
    }

    /**
     * @param Backup|Wallet $toAccount
     */
    private function persistExport($toAccount): void
    {
        $contractor = $this->getContractor();
        $toAccount->setContractor($contractor);
        $this->entityManager->persist($toAccount);
        $this->entityManager->flush();
    }

    private function getContractor(): Contractor
    {
        return $this->contractorRepository->findOneBy([
            'description' => ContractorRepository::INTERNAL_TRANSFER
        ]);
    }


}
