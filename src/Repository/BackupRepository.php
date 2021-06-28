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
class BackupRepository extends ServiceEntityRepository implements AppPaginatorInterface
{
    use WalletBackup;

    private const PAYMENTS_BY_MONTH_YEARS = 36;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backup::class);
    }

    public function paymentsByMonth()
    {
        return $this->createQueryBuilder('p')
            ->select('p.yearMonth', 'SUM(p.amount) as sa')
            ->where('p.amount > 99')
            ->groupBy('p.yearMonth')
            ->orderBy('p.yearMonth', 'DESC')
            ->setMaxResults(self::PAYMENTS_BY_MONTH_YEARS)
            ->getQuery()
            ->getResult();
    }
}
