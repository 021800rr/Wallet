<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Contractor;
use App\Entity\Fee;
use Doctrine\ORM\QueryBuilder;

interface AppRepositoryInterface
{
    /** @return Fee[]|Contractor[]|AbstractAccount[] */
    public function findAll(): array;

    public function getAllRecordsQueryBuilder(): QueryBuilder;
}
