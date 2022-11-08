<?php

namespace App\Service\BalanceUpdater;

use App\Repository\AccountRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class AbstractBalanceUpdater implements BalanceUpdaterInterface
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, int $id): void
    {
        list($predecessor, $transaction, $successors) = $this->setUp($accountRepository, $id);
        $this->walk($predecessor, $transaction, $successors);
    }

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @return array
     *      [
     *          ?AbstractWallet|?Backup,
     *          ?AbstractWallet|?Backup,
     *          ?AbstractWallet[]|?Backup[]
     *      ]
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
                try {
                    $predecessor = $transactions[$key + 1];
                } catch (Exception $exception) {
                    throw new Exception("must not modify the first record");
                }

                return [$predecessor, $transaction, $successors];
            }
            array_unshift($successors, $transaction);
        }

        throw new Exception("no transactions");
    }
}
