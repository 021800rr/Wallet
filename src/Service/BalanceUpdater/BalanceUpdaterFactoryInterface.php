<?php

namespace App\Service\BalanceUpdater;

interface BalanceUpdaterFactoryInterface
{
    public function create(): BalanceUpdaterAccountInterface;
}
