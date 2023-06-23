<?php

namespace App\Service\BalanceUpdater;

use App\Repository\AccountRepositoryInterface;
use Exception;

trait BalanceUpdaterTrait
{
    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, int $id): void
    {
        list($predecessor, $transaction, $successors) = $this->setUp($accountRepository, $id);
        $this->walk($accountRepository, $predecessor, $transaction, $successors);
    }
}
