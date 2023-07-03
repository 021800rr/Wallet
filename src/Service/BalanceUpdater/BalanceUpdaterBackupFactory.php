<?php

namespace App\Service\BalanceUpdater;

readonly class BalanceUpdaterBackupFactory implements BalanceUpdaterFactoryInterface
{
    public function __construct(private BalanceUpdaterAccountInterface $backupUpdater)
    {
    }

    public function create(): BalanceUpdaterAccountInterface
    {
        return $this->backupUpdater;
    }
}
