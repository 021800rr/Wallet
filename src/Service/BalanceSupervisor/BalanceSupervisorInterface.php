<?php

namespace App\Service\BalanceSupervisor;

use App\Repository\AccountRepositoryInterface;
use Generator;

interface BalanceSupervisorInterface
{
    public function setWallets(array $wallets);

    public function crawl(AccountRepositoryInterface $repository): Generator;
}
