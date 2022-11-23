<?php

namespace App\Service\BalanceSupervisor;

use App\Repository\AccountRepositoryInterface;

interface BalanceSupervisorInterface
{
    public function setWallets(array $wallets);

    public function crawl(AccountRepositoryInterface $repository);
}
