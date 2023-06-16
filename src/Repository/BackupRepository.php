<?php

namespace App\Repository;

use App\Entity\Backup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Backup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Backup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Backup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackupRepository extends ServiceEntityRepository implements BackupRepositoryInterface
{
    use AccountTrait;
    use AppTrait;

    private const PAYMENTS_BY_MONTH_YEARS = 12;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backup::class);
    }

    public function save(Backup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Backup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function paymentsByMonth(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.yearMonth', 'SUM(p.amount) as sum_of_amount')
            ->groupBy('p.yearMonth')
            ->orderBy('p.yearMonth', 'DESC')
            ->setMaxResults(self::PAYMENTS_BY_MONTH_YEARS)
            ->getQuery()
            ->getResult();
    }
}
