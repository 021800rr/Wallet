<?php

namespace App\Repository;

use App\Entity\Wallet;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface WalletRepositoryInterface extends AccountRepositoryInterface
{
    public function save(Wallet $entity, bool $flush = false): void;

    public function remove(Wallet $entity, bool $flush = false): void;

    public function search(string $data, int $offset): Paginator;
}
