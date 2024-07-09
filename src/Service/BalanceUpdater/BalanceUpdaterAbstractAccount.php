<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
use App\Repository\AccountRepositoryInterface;
use Exception;

abstract class BalanceUpdaterAbstractAccount implements BalanceUpdaterAccountInterface
{
    public function __construct(protected ?int $previousId = null)
    {
    }

    /**
     * @throws Exception
     */
    public function setPreviousId(AccountRepositoryInterface $accountRepository, ?int $id): ?int
    {
        $reversed = array_reverse($accountRepository->findAll());
        foreach ($reversed as $key => $transaction) {
            $this->previousId = $transaction->getId();
            if (0 === $key && ($transaction->getId() === $id || ($reversed[1])->getId() === $id)) {
                $this->previousId = null;
                throw new Exception("the first two records cannot be changed.... (for now)");
            } elseif (isset($reversed[$key + 1]) && $reversed[$key + 1]->getId() === $id) {
                break;
            }
        }

        return $this->previousId;
    }

    /**
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, ?int $id): void
    {
        $selectedId = $this->getOlderRecordId($accountRepository, (int) $id);

        /**
         * @var AbstractAccount $predecessor
         * @var AbstractAccount $transaction
         * @var AbstractAccount[] $successors
         */
        list($predecessor, $transaction, $successors) = $this->setUp($accountRepository, $selectedId);
        $this->walk($accountRepository, $predecessor, $transaction, $successors);
    }

    /**
     * @throws Exception
     */
    protected function getOlderRecordId(AccountRepositoryInterface $accountRepository, int $id): int
    {
        if (!$this->previousId) {
            throw new Exception('no previous id');
        }
        $a = $accountRepository->find($this->previousId);
        $b = $accountRepository->find($id);

        if ($a->getDate() < $b->getDate()) {
            $result = $a;
        } elseif ($b->getDate() < $a->getDate()) {
            $result = $b;
        } elseif ($a->getId() < $b->getId()) {
            $result = $a;
        } else {
            $result = $b;
        }

        return $result->getId();
    }

    /**
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

    /**
     * @param array<int, AbstractAccount>|null $successors
     */
    abstract protected function walk(
        AccountRepositoryInterface $accountRepository,
        AbstractAccount            $predecessor,
        AbstractAccount            $transaction,
        ?array                     $successors,
    ): void;
}
