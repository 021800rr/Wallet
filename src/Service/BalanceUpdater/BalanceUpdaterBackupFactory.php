<?php

namespace App\Service\BalanceUpdater;

readonly class BalanceUpdaterBackupFactory implements BalanceUpdaterFactoryInterface
{
    public function __construct(private BalanceUpdaterAccountInterface $backup)
    {
    }

    public function create(): BalanceUpdaterAccountInterface
    {
        return $this->backup;
    }
}
