<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
use App\Repository\AccountRepositoryInterface;
use Exception;

abstract class BalanceUpdaterAbstractAccount implements BalanceUpdaterAccountInterface
{
    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @return array<int, AbstractAccount|AbstractAccount[]|null>
     * @throws Exception
     */
    protected function setUp(AccountRepositoryInterface $accountRepository, int $id): array
    {
        /** @var AbstractAccount[] $transactions */
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
