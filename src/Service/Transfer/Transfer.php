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
    public function moveToBackup(Backup $backup): void
    {
        $this->persistExport($backup);
        $this->persistImport(new Wallet(), $backup);

        $this->backupUpdater->compute($this->backupRepository);
        $this->walletUpdater->compute($this->walletRepository);
    }

    /**
     * @throws Exception
     */
    public function moveToWallet(Wallet $wallet): void
    {
        $this->persistExport($wallet);
        $this->persistImport(new Backup(), $wallet);

        $this->walletUpdater->compute($this->walletRepository);
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
