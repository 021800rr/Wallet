<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractWallet;
use App\Entity\Backup;
use App\Repository\AccountRepositoryInterface;
use Exception;

abstract class AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @throws Exception
     */
    abstract public function compute(AccountRepositoryInterface $accountRepository, int $id): void;

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @return array<int, AbstractWallet|Backup|AbstractWallet[]|Backup[]|null>
     * @throws Exception
     */
    protected function setUp(AccountRepositoryInterface $accountRepository, int $id): array
    {
        $transactions = $accountRepository->findAll();
        if (2 >= count($transactions)) {
            throw new Exception("I cravenly refuse to perform this operation");
        }
        $successors = [];
        foreach ($transactions as $key => $transaction) {
            if ($id === $transaction->getId()) {
                $predecessor = $transactions[$key + 1];

                return [$predecessor, $transaction, $successors];
            }
            array_unshift($successors, $transaction);
        }

        throw new Exception("no transactions");
    }
}
