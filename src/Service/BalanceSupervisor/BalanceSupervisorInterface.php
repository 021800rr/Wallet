<?php

namespace App\Service\BalanceSupervisor;

use App\Entity\AbstractWallet;
use App\Repository\AccountRepositoryInterface;
use Generator;

interface BalanceSupervisorInterface
{
    /**
     * @param AbstractWallet[] $wallets
     * @return void
     */
    public function setWallets(array $wallets): void;

    public function crawl(AccountRepositoryInterface $accountRepository): Generator;
}
