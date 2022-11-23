<?php

namespace App\Repository;

use App\Entity\Backup;
use App\Entity\AbstractWallet;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait AccountTrait
{
    /** @return AbstractWallet[]|Backup[] array */
    public function findAll(): array
    {
        return $this->findBy([], ['date' => 'DESC', 'id' => 'DESC']);
    }

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

    public function getPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('w')
            ->addOrderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->setMaxResults(PaginatorEnum::PerPage->value)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
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
