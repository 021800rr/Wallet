<?php

namespace App\Repository;

use App\Entity\Backup;

interface BackupRepositoryInterface extends AccountRepositoryInterface
{
    public function save(Backup $entity, bool $flush = false): void;

    public function remove(Backup $entity, bool $flush = false): void;

    public function paymentsByMonth(): array;
}
