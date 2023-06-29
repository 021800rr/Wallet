<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Chf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChfRepository extends ServiceEntityRepository implements AccountRepositoryInterface
{
    use AccountTrait;
    use AppTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chf::class);
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
