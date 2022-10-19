<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractWallet;
use App\Entity\Backup;

class BackupBalanceUpdater extends AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    protected function walk(AbstractWallet|Backup $predecessor, AbstractWallet|Backup $transaction, ?array $successors): void
    {
        /** @var Backup $transaction */
        if (Backup::INAPPLICABLE === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
            $transaction = $this->setSubWallets($predecessor, $transaction);
        } elseif (Backup::NOT_PROCESSED === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getBalance());
            $transaction->setRetiring($predecessor->getRetiring() + $transaction->getRetiring());
            $transaction->setHoliday($predecessor->getHoliday() + $transaction->getHoliday());
            $transaction->setInterest(Backup::DONE);
        } elseif (Backup::DONE === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        }
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
        if (0.0 < $transaction->getAmount()) {
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
