<?php

namespace App\Service;

use App\Entity\Backup;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class BalanceUpdater implements UpdaterInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param WalletRepository|BackupRepository $transactionRepository
     * @throws Exception
     */
    public function compute($transactionRepository): void
    {
        list($predecessor, $transaction, $successors) = $this->setUp($transactionRepository);
        $this->walk($predecessor, $transaction, $successors);
    }

    /**
     * @param WalletRepository|BackupRepository $transactionRepository
     * @return array [Wallet|Backup, Wallet|Backup, ?Wallet[]|?Backup[]]
     * @throws Exception
     */
    protected function setUp($transactionRepository): array
    {
        $transactions = array_reverse($transactionRepository->findAll());
        if (2 > count($transactions)) {
            throw new Exception("I cravenly refuse to perform this operation");
        }
        $predecessor = $transactions[0];
        $transaction = $transactions[1];
        $successors = array_slice($transactions, 2);

        return [$predecessor, $transaction, $successors];
    }

    /**
     * @param Wallet|Backup $predecessor
     * @param Wallet|Backup $transaction
     * @param ?Backup[]|?Wallet[] $successors
     */
    abstract protected function walk($predecessor, &$transaction, ?array $successors): void;
}
