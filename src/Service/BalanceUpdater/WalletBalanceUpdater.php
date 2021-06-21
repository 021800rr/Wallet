<?php

namespace App\Service\BalanceUpdater;

class WalletBalanceUpdater extends AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    protected function walk($predecessor, &$transaction, ?array $successors): void
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
