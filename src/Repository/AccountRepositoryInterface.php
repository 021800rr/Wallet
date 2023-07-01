<?php

namespace App\Repository;

use App\Entity\AbstractAccount;

interface AccountRepositoryInterface extends AppRepositoryInterface
{
    public function getCurrentBalance(): float;

    public function getLastRecord(): mixed;

    /**
     * @return AbstractAccount[]
     */
    public function getAllRecords(): array;

    public function find($id, $lockMode = null, $lockVersion = null);

    public function save(AbstractAccount $entity, bool $flush = false): void;

    public function remove(AbstractAccount $entity, bool $flush = false): void;
}
