<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
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
        /**
         * @var AbstractAccount $predecessor
         * @var AbstractAccount $transaction
         * @var AbstractAccount[] $successors
         */
        list($predecessor, $transaction, $successors) = $this->setUp($accountRepository, $id);
        $this->walk($accountRepository, $predecessor, $transaction, $successors);
    }
}
