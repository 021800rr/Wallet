<?php

namespace App\Repository;

use App\Entity\Chf;

interface ChfRepositoryInterface extends AccountRepositoryInterface
{
    public function save(Chf $entity, bool $flush = false): void;

    public function remove(Chf $entity, bool $flush = false): void;
}
