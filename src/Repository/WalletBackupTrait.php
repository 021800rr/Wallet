<?php

namespace App\Repository;

use App\Entity\Backup;
use App\Entity\AbstractWallet;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait WalletBackupTrait
{
    /** @var AbstractWallet[]|Backup[] array */
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
            ->setMaxResults(AppPaginatorInterface::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }
}
