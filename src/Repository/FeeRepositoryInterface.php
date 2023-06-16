<?php

namespace App\Repository;

use App\Entity\Fee;

interface FeeRepositoryInterface extends AppRepositoryInterface
{
    public function save(Fee $entity, bool $flush = false): void;

    public function remove(Fee $entity, bool $flush = false): void;
}
