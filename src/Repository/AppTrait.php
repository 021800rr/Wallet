<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Fee;
use Doctrine\ORM\QueryBuilder;

trait AppTrait
{
    /** @return AbstractAccount[]|Fee[] */
    public function findAll(): array
    {
        return $this->findBy([], ['date' => 'DESC', 'id' => 'DESC']);
    }

    public function getAllRecordsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('w')
            ->addOrderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC');
    }
}
