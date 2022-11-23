<?php

namespace App\Repository;

interface WalletRepositoryInterface extends AccountRepositoryInterface
{
    public function search(string $data, int $offset);
}
