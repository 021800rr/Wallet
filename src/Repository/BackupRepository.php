<?php

namespace App\Repository;

use App\Entity\Backup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Backup>
 * @method Backup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Backup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Backup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackupRepository extends ServiceEntityRepository implements BackupRepositoryInterface
{
    use AccountTrait;
    use AppTrait;
    use SaveRemoveTrait;

    private const PAYMENTS_BY_MONTH_YEARS = 12;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backup::class);
    }

    /** @return array<int, array<string, string>> */
    public function paymentsByMonth(): array
    {
        $result = $this->createQueryBuilder('p')
           ->select('p.yearMonth', 'SUM(p.amount) as sum_of_amounts')
           ->groupBy('p.yearMonth')
           ->orderBy('p.yearMonth', 'DESC')
           ->setMaxResults(self::PAYMENTS_BY_MONTH_YEARS)
           ->getQuery()
           ->getResult();

        if (!is_array($result)) {
            return [];
        }

        return $result;
    }
}
