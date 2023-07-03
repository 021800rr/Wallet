<?php

namespace App\Repository;

interface BackupRepositoryInterface extends AccountRepositoryInterface
{
    /**
     * @return array<int, array<string, string>>
     */
    public function paymentsByMonth(): array;
}
