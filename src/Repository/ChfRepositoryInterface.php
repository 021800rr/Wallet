<?php

namespace App\Repository;

use App\Entity\Chf;

interface ChfRepositoryInterface extends AccountRepositoryInterface
{
    public function remove(Chf $entity, bool $flush = false): void;
}
