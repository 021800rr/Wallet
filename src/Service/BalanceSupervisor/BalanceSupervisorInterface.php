<?php

namespace App\Service\BalanceSupervisor;

interface BalanceSupervisorInterface
{
    public function setWallets(array $wallets);

    public function crawl();
}
