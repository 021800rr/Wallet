<?php

namespace App\Service\BalanceSupervisor;

use App\Entity\Chf;
use App\Entity\Wallet;
use App\Repository\AccountRepositoryInterface;
use Generator;

interface BalanceSupervisorInterface
{
    /**
     * @param Chf[]|Wallet[] $wallets
     * @return void
     */
    public function setWallets(array $wallets): void;

    public function crawl(AccountRepositoryInterface $accountRepository): Generator;
}
