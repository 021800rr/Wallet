<?php

namespace App\Repository;

use App\Entity\Contractor;

interface ContractorRepositoryInterface extends AppRepositoryInterface
{
    public function save(Contractor $entity, bool $flush = false): void;

    public function remove(Contractor $entity, bool $flush = false): void;

    public function getInternalTransferOwner(): Contractor;
}
