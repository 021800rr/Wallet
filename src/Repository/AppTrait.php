<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Fee;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait AppTrait
{
    /** @return AbstractAccount[]|Fee[] */
    public function findAll(): array
    {
        return $this->findBy([], ['date' => 'DESC', 'id' => 'DESC']);
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
}
