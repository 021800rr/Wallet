<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Wallet;

class WalletBalanceUpdater extends BalanceUpdater implements UpdaterInterface
{
    protected function walk(Wallet|Backup $predecessor, Wallet|Backup &$transaction, ?array $successors): void
    {
        $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        if (count($successors)) {
            $predecessor = $transaction;
            $transaction = array_shift($successors);
            $this->walk($predecessor, $transaction, $successors);
        }
    }
}
