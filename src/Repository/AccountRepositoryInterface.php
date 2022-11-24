<?php

namespace App\Repository;

interface AccountRepositoryInterface extends AppRepositoryInterface
{
    public function getCurrentBalance(): float;

    public function getLastRecord(): mixed;

    public function getAllRecords(): array;
}
