<?php

namespace App\Service\BalanceUpdater;

use App\Repository\AccountRepositoryInterface;
use Exception;

interface BalanceUpdaterAccountInterface
{
    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, int $id): void;
}
