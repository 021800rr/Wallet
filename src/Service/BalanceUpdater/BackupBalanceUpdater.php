<?php

namespace App\Service\BalanceUpdater;

use App\Entity\Backup;

class BackupBalanceUpdater extends AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    protected function walk($predecessor, $transaction, ?array $successors): void
    {
        $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        $transaction = $this->setSubWallets($predecessor, $transaction);
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        if (count($successors)) {
            $predecessor = $transaction;
            $transaction = array_shift($successors);
            $this->walk($predecessor, $transaction, $successors);
        }
    }

    private function setSubWallets(Backup $predecessor, Backup $transaction): Backup
    {
        if (0 < $transaction->getAmount()) {
            $transaction->setRetiring($predecessor->getRetiring() + $transaction->getAmount() / 2);
            $transaction->setHoliday($predecessor->getHoliday() + $transaction->getAmount() / 2);
        } else {
            $transaction->setRetiring($predecessor->getRetiring());
            $transaction->setHoliday(
                $predecessor->getHoliday() + $transaction->getAmount()
            );
        }
        return $transaction;
    }
}
