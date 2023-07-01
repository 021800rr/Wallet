<?php

namespace App\Repository;

use App\Entity\Contractor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contractor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contractor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contractor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractorRepository extends ServiceEntityRepository implements ContractorRepositoryInterface
{
    public const INTERNAL_TRANSFER = 'Przelew własny';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contractor::class);
    }

    public function save(Contractor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contractor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAll(): array
    {
        return $this->findBy([], ['description' => 'ASC']);
    }

    public function getPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->addOrderBy('c.description', 'ASC')
            ->setMaxResults(PaginatorEnum::PerPage->value)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function getInternalTransferOwner(): ?Contractor
    {
        return $this->findOneBy([
            'description' => ContractorRepository::INTERNAL_TRANSFER
        ]);
    }
}
