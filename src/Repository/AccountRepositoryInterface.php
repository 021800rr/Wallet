<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Backup;
use App\Entity\Chf;
use App\Entity\Eur;
use App\Entity\Wallet;

interface AccountRepositoryInterface extends AppRepositoryInterface
{
    public function getCurrentBalance(): float;

    public function getLastRecord(): mixed;

    /**
     * @return Backup[]|Chf[]|Eur[]|Wallet[]
     */
    public function getAllRecords(): array;

    public function find($id, $lockMode = null, $lockVersion = null);

    public function save(AbstractAccount $entity, bool $flush = false): void;
}
