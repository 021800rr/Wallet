<?php

namespace App\Repository;

use App\Entity\Backup;

interface BackupRepositoryInterface extends AccountRepositoryInterface
{
    public function remove(Backup $entity, bool $flush = false): void;

    /**
     * @return array<int, array<string, string>>
     */
    public function paymentsByMonth(): array;
}
