<?php

namespace App\Service\BalanceUpdater;

use App\Repository\AccountRepositoryInterface;
use Exception;

interface BalanceUpdaterAccountInterface
{
    public function setPreviousId(AccountRepositoryInterface $accountRepository, int $id): ?int;

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, int $id): void;
}
