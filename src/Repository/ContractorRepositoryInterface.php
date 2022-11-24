<?php

namespace App\Repository;

use App\Entity\Contractor;

interface ContractorRepositoryInterface extends AppRepositoryInterface
{
    public function getInternalTransferOwner(): Contractor;
}
