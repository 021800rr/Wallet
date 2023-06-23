<?php

namespace App\Repository;

use App\Entity\Backup;
use App\Entity\Chf;
use App\Entity\Contractor;
use App\Entity\Eur;
use App\Entity\Fee;
use App\Entity\Wallet;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface AppRepositoryInterface
{
    /**
     * @return Fee[]|Contractor[]|Backup[]|Chf[]|Eur[]|Wallet[]
     */
    public function findAll(): array;

    public function getPaginator(int $offset): Paginator;
}
