<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Entity\Wallet;
use App\Repository\ContractorRepository;
use Exception;

class BackupBalanceUpdater extends BalanceUpdater
{
    private Contractor $contractor;

    /**
     * @param Wallet|Backup $toWallet
     * @throws Exception
     */
    public function moveAssets($toWallet): void
    {
        $this->contractor = $this->getContractor();
        $this->persistExport($toWallet);
        $this->persistImport($toWallet);

        $this->compute($this->backupRepository);
        $this->compute($this->walletRepository);
    }

    private function getContractor(): Contractor
    {
        return $this->contractorRepository->findOneBy([
            'description' => ContractorRepository::INTERNAL_TRANSFER
        ]);
    }

    /** @param Backup|Wallet $toWallet */
    private function persistExport($toWallet): void
    {
        $toWallet->setContractor($this->contractor);
        $this->entityManager->persist($toWallet);
        $this->entityManager->flush();
    }

    /**
     * @param Backup|Wallet $toWallet
     * @throws Exception
     */
    private function persistImport($toWallet): void
    {
        /** @var Wallet|Backup $fromWallet */
        $fromWallet = $this->getFromWallet($toWallet);
        $fromWallet->setContractor($this->contractor);
        $fromWallet->setAmount(-1 * $toWallet->getAmount());
        $this->entityManager->persist($fromWallet);
        $this->entityManager->flush();
    }

    /**
     * @param Wallet|Backup $toWallet
     * @return Backup|Wallet
     * @throws Exception
     */
    private function getFromWallet($toWallet)
    {
        switch (get_class($toWallet)) {
            case "App\\Entity\\Backup":
                return new Wallet();
            case "App\\Entity\\Wallet":
                return new Backup();
            default:
                throw new Exception(__METHOD__);
        }
    }

    protected function walk($predecessor, &$transaction, ?array $successors): void
    {
        $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        $this->setSubWallets($predecessor, $transaction);
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        if (count($successors)) {
            $predecessor = $transaction;
            $transaction = array_shift($successors);
            $this->walk($predecessor, $transaction, $successors);
        }
    }

    private function setSubWallets($predecessor, &$transaction): void
    {
        if ('App\\Entity\\Backup' === get_class($transaction)) {
            if (0 < $transaction->getAmount()) {
                $transaction->setRetiring($predecessor->getRetiring() + $transaction->getAmount() / 2);
                $transaction->setHoliday($predecessor->getHoliday() + $transaction->getAmount() / 2);
            } else {
                $transaction->setRetiring($predecessor->getRetiring());
                $transaction->setHoliday($predecessor->getHoliday() + $transaction->getAmount()
                );
            }
        }
    }
}
