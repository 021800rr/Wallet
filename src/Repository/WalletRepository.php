<?php

namespace App\Repository;

use App\Entity\AbstractAccount;
use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wallet>
 *
 * @method Wallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WalletRepository extends ServiceEntityRepository implements WalletRepositoryInterface
{
    use AccountTrait;
    use AppTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
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
}
