<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
use App\Repository\AccountRepositoryInterface;

class BalanceUpdaterWallet extends BalanceUpdaterAbstractAccount implements BalanceUpdaterAccountInterface
{
    /**
     * @param array<int, AbstractAccount>|null $successors
     */
    protected function walk(
        AccountRepositoryInterface $accountRepository,
        AbstractAccount $predecessor,
        AbstractAccount $transaction,
        ?array $successors,
    ): void {
        $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        $accountRepository->save($transaction, true);

        /** @var array<int, AbstractAccount> $successors */
        if (count($successors)) {
            $predecessor = $transaction;
            $transaction = array_shift($successors);
            $this->walk($accountRepository, $predecessor, $transaction, $successors);
        }
    }
}
