<?php

namespace App\Repository;

use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WalletRepository extends ServiceEntityRepository implements AppPaginatorInterface
{
    use WalletBackup;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    public function search(string $data, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('w')
            ->leftJoin('w.contractor', 'c')
            ->where('w.amount = :amount')
            ->orWhere('w.balance = :balance')
            ->orWhere('LOWER(c.description) like LOWER(:contractor)')
            ->setParameter('amount', (float)$data)
            ->setParameter('balance', (float)$data)
            ->setParameter('contractor', '%' . $data . '%')
            ->addOrderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->setMaxResults(WalletRepository::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }
}
