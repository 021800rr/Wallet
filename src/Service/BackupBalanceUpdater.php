<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Wallet;

class BackupBalanceUpdater extends BalanceUpdater implements UpdaterInterface
{
    protected function walk(Wallet|Backup $predecessor, Wallet|Backup &$transaction, ?array $successors): void
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

    private function setSubWallets(Backup $predecessor, Backup &$transaction): void
    {
        if ('App\\Entity\\Backup' === get_class($transaction)) {
            if (0 < $transaction->getAmount()) {
                $transaction->setRetiring($predecessor->getRetiring() + $transaction->getAmount() / 2);
                $transaction->setHoliday($predecessor->getHoliday() + $transaction->getAmount() / 2);
            } else {
                $transaction->setRetiring($predecessor->getRetiring());
                $transaction->setHoliday(
                    $predecessor->getHoliday() + $transaction->getAmount()
                );
            }
        }
    }
}
