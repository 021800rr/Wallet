<?php

namespace App\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

trait AccountTrait
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getCurrentBalance(): float
    {
        return $this->createQueryBuilder('w')
            ->select('w.balance')
            ->orderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleScalarResult();
    }

    /**
     * @return int|mixed|string
     * @throws NonUniqueResultException
     */
    public function getLastRecord(): mixed
    {
        return $this->createQueryBuilder('w')
            ->orderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function getAllRecords(): array
    {
        return array_reverse(
            $this->createQueryBuilder('w')
                ->addOrderBy('w.date', 'DESC')
                ->addOrderBy('w.id', 'DESC')
                ->getQuery()
                ->getResult()
        );
    }
}
