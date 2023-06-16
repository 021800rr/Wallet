<?php

namespace App\Repository;

use App\Entity\Eur;

interface EurRepositoryInterface extends AccountRepositoryInterface
{
    public function save(Eur $entity, bool $flush = false): void;

    public function remove(Eur $entity, bool $flush = false): void;
}
