<?php

namespace App\Repository;

use App\Entity\Pln;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pln>
 *
 * @method Pln|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pln|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pln[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlnRepository extends ServiceEntityRepository implements AccountRepositoryInterface, PlnSearchRepositoryInterface
{
    use AccountTrait;
    use AppTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pln::class);
    }

    public function search(string $data, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('w')
            ->innerJoin('w.contractor', 'c')
            ->where('LOWER(c.description) like LOWER(:contractor)')
            ->orWhere('LOWER(w.description) like LOWER(:description)')
            ->setParameter('contractor', '%' . $data . '%')
            ->setParameter('description', '%' . $data . '%')
            ->addOrderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC');

        if ((float) $data) {
            $query
                ->orWhere('w.amount = :amount')
                ->orWhere('w.balance = :balance')
                ->setParameter('amount', (float) $data)
                ->setParameter('balance', (float) $data);
        }

        $query
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function getAllRecordsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('w')
            ->addOrderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC');
    }
}
