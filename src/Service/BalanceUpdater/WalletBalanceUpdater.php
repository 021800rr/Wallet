<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractWallet;
use App\Entity\Backup;

class WalletBalanceUpdater extends AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    protected function walk(AbstractWallet|Backup $predecessor, AbstractWallet|Backup $transaction, ?array $successors): void
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
