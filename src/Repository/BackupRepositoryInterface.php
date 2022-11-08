<?php

namespace App\Repository;

interface BackupRepositoryInterface extends AccountRepositoryInterface
{
    public function paymentsByMonth();
}
