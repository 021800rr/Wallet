<?php

namespace App\Service\BalanceUpdater;

use App\Entity\Backup;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param WalletRepository|BackupRepository $transactionRepository
     * @param int $id
     * @throws Exception
     */
    public function compute($transactionRepository, int $id): void
    {
        list($predecessor, $transaction, $successors) = $this->setUp($transactionRepository, $id);
        $this->walk($predecessor, $transaction, $successors);
    }

    /**
     * @param WalletRepository|BackupRepository $transactionRepository
     * @param int $id
     * @return array [?Wallet|?Backup, ?Wallet|?Backup, ?Wallet[]|?Backup[]]
     * @throws Exception
     */
    protected function setUp($transactionRepository, int $id): array
    {
        $transactions = array_reverse($transactionRepository->findAll());
        if (2 > count($transactions)) {
            throw new Exception("I cravenly refuse to perform this operation");
        }
        foreach ($transactions as $key => $transaction) {
            if ($id === $transaction->getId()) {
                $predecessor = $transactions[$key - 1];
                $successors = array_slice($transactions, $key + 1);

                return [$predecessor, $transaction, $successors];
            }
        }

        return [null, null, []];
    }

    /**
     * @param Wallet|Backup $predecessor
     * @param Wallet|Backup $transaction
     * @param ?Backup[]|?Wallet[] $successors
     */
    abstract protected function walk($predecessor, $transaction, ?array $successors): void;
}
