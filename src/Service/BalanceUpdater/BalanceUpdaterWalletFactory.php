<?php

namespace App\Service\BalanceUpdater;

readonly class BalanceUpdaterWalletFactory implements BalanceUpdaterFactoryInterface
{
    public function __construct(private BalanceUpdaterAccountInterface $walletUpdater)
    {
    }

    public function create(): BalanceUpdaterAccountInterface
    {
        return $this->walletUpdater;
    }
}
