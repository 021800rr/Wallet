<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Contractor;
use App\Entity\Fee;

trait SaveRemoveTrait
{
    public function save(AbstractAccount|Contractor|Fee $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AbstractAccount|Contractor|Fee $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
