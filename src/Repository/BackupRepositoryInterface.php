<?php

namespace App\Repository;

use App\Entity\Backup;

interface BackupRepositoryInterface extends AccountRepositoryInterface
{
    /**
     * @return array<int, array<string, string>>
     */
    public function paymentsByMonth(): array;
}
