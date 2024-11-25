<?php

namespace App\Repository;

use App\Entity\Contractor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contractor>
 * @method Contractor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contractor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contractor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractorRepository extends ServiceEntityRepository implements ContractorRepositoryInterface
{
    use SaveRemoveTrait;

    public const INTERNAL_TRANSFER = 'Przelew własny';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contractor::class);
    }

    public function findAll(): array
    {
        return $this->findBy([], ['description' => 'ASC']);
    }

    public function getAllRecordsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->addOrderBy('c.description', 'ASC');
    }

    public function getInternalTransferOwner(): ?Contractor
    {
        return $this->findOneBy([
            'description' => ContractorRepository::INTERNAL_TRANSFER
        ]);
    }
}
