<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractWallet;
use App\Entity\Backup;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\EurRepository;
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
     * @param WalletRepository|ChfRepository|EurRepository|BackupRepository $transactionRepository
     * @param int $id
     * @throws Exception
     */
    public function compute(WalletRepository|ChfRepository|EurRepository|BackupRepository $transactionRepository, int $id): void
    {
        list($predecessor, $transaction, $successors) = $this->setUp($transactionRepository, $id);
        $this->walk($predecessor, $transaction, $successors);
    }

    /**
     * @param WalletRepository|ChfRepository|EurRepository|BackupRepository $transactionRepository
     * @param int $id
     * @return array [?AbstractWallet|?Backup, ?AbstractWallet|?Backup, ?AbstractWallet[]|?Backup[]]
     * @throws Exception
     */
    protected function setUp(WalletRepository|ChfRepository|EurRepository|BackupRepository $transactionRepository, int $id): array
    {
        $transactions = $transactionRepository->findAll();
        if (2 >= count($transactions)) {
            throw new Exception("I cravenly refuse to perform this operation");
        }
        $successors = [];
        foreach ($transactions as $key => $transaction) {
            if ($id === $transaction->getId()) {
                try {
                    $predecessor = $transactions[$key + 1];
                } catch (Exception $exception) {
                    throw new Exception("must not modify the first record");
                }

                return [$predecessor, $transaction, $successors];
            }
            array_unshift($successors, $transaction);
        }

        throw new Exception("no transactions or what? :p");
    }

    /**
     * @param AbstractWallet|Backup $predecessor
     * @param AbstractWallet|Backup $transaction
     * @param ?Backup[]|?AbstractWallet[] $successors
     */
    abstract protected function walk(AbstractWallet|Backup $predecessor, AbstractWallet|Backup $transaction, ?array $successors): void;
}
