<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
use App\Repository\AccountRepositoryInterface;

class BalanceUpdaterWallet extends BalanceUpdaterAbstractAccount implements BalanceUpdaterAccountInterface
{
    use BalanceUpdaterTrait;

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param AbstractAccount $predecessor
     * @param AbstractAccount $transaction
     * @param AbstractAccount[]|null $successors
     * @return void
     */
    protected function walk(
        AccountRepositoryInterface $accountRepository,
        AbstractAccount $predecessor,
        AbstractAccount $transaction,
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
