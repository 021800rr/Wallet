<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Eur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Eur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EurRepository extends ServiceEntityRepository implements AccountRepositoryInterface
{
    use AccountTrait;
    use AppTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eur::class);
    }

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
}
