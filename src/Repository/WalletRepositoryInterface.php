<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface WalletRepositoryInterface extends AccountRepositoryInterface
{
    public function search(string $data, int $offset): Paginator;
}
