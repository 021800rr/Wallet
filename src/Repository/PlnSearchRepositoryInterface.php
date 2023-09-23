<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;

interface PlnSearchRepositoryInterface
{
    public function search(string $data): QueryBuilder;
}
