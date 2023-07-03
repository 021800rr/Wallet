<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Contractor;
use App\Entity\Fee;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface AppRepositoryInterface
{
    /** @return Fee[]|Contractor[]|AbstractAccount[] */
    public function findAll(): array;

    public function getPaginator(int $offset): Paginator;
}
