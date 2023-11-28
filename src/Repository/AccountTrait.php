<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
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
        $result = $this->createQueryBuilder('w')
            ->select('w.balance')
            ->orderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleScalarResult();

        if (is_int($result) || is_string($result)) {
            $result = (float) $result;
        } elseif (!is_float($result)) {
            $result = 0.0;
        }

        return $result;
    }

    /**
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
        $array = $this->createQueryBuilder('w')
            ->addOrderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->getQuery()
            ->getResult();

        if (!is_array($array)) {
            return [];
        }

        return array_reverse($array);
    }
}
