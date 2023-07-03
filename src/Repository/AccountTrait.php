<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

trait AccountTrait
{
    public function save(AbstractAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AbstractAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

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
