<?php

namespace App\Service\BalanceUpdater;

readonly class BalanceUpdaterWalletFactory implements BalanceUpdaterFactoryInterface
{
    public function __construct(private BalanceUpdaterAccountInterface $wallet)
    {
    }

    public function create(): BalanceUpdaterAccountInterface
    {
        return $this->wallet;
    }
}
