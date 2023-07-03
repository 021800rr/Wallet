<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface PlnSearchRepositoryInterface
{
    public function search(string $data, int $offset): Paginator;
}
