<?php

namespace App\Repository;

use App\Entity\Eur;

interface EurRepositoryInterface extends AccountRepositoryInterface
{
    public function remove(Eur $entity, bool $flush = false): void;
}
