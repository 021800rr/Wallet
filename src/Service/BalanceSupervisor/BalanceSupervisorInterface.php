<?php

namespace App\Service\BalanceSupervisor;

use App\Entity\AbstractAccount;
use App\Repository\AccountRepositoryInterface;
use Generator;

interface BalanceSupervisorInterface
{
    /**
     * @param AbstractAccount[] $wallets
     * @return void
     */
    public function setWallets(array $wallets): void;

    public function crawl(AccountRepositoryInterface $accountRepository): Generator;
}
