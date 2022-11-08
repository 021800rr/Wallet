<?php

namespace App\Repository;

interface ContractorRepositoryInterface extends AppRepositoryInterface
{
    public function getInternalTransferOwner();
}
