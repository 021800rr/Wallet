<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface AppRepositoryInterface
{
    public function findAll(): array;

    public function getPaginator(int $offset): Paginator;
}
