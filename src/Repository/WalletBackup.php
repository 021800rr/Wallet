<?php

namespace App\Repository;

use App\Entity\Backup;
use App\Entity\Wallet;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait WalletBackup
{
    /** @var Wallet[]|Backup[] array */
    public function findAll(): array
    {
        return $this->findBy([], ['date' => 'DESC', 'id' => 'DESC']);
    }

    public function getCurrentBalance(): float
    {
        return $this->createQueryBuilder('w')
            ->select('w.balance')
            ->orderBy('w.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleScalarResult();
    }

    public function getPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('w')
            ->addOrderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->setMaxResults(WalletRepository::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }
}
