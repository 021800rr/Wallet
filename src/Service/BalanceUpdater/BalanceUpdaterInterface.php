<?php

namespace App\Service\BalanceUpdater;

use App\Repository\AccountRepositoryInterface;
use Exception;

interface BalanceUpdaterInterface
{
    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, int $id): void;
}
