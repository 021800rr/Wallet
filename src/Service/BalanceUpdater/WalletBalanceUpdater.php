<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractWallet;
use App\Repository\AccountRepositoryInterface;

class WalletBalanceUpdater extends AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    use BalanceUpdaterTrait;

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param AbstractWallet $predecessor
     * @param AbstractWallet $transaction
     * @param AbstractWallet[]|null $successors
     * @return void
     */
    protected function walk(
        AccountRepositoryInterface $accountRepository,
        AbstractWallet $predecessor,
        AbstractWallet $transaction,
        ?array $successors
    ): void {
        $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        $accountRepository->save($transaction, true);

        if (count($successors)) {
            $predecessor = $transaction;
            $transaction = array_shift($successors);
            $this->walk($accountRepository, $predecessor, $transaction, $successors);
        }
    }
}
