<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface AppRepositoryInterface extends FeeRepositoryInterface
{
    public function getPaginator(int $offset): Paginator;
}
