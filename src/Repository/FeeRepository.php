<?php

namespace App\Repository;

use App\Entity\Fee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fee>
 * @method Fee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeeRepository extends ServiceEntityRepository implements FeeRepositoryInterface
{
    use AppTrait;
    use SaveRemoveTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fee::class);
    }
}
