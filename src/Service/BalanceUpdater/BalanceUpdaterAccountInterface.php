<?php

namespace App\Service\BalanceUpdater;

use App\Repository\AccountRepositoryInterface;
use Exception;

interface BalanceUpdaterAccountInterface
{
    public function setPreviousId(AccountRepositoryInterface $accountRepository, int $id): ?int;

    /**
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, int $id): void;
}
