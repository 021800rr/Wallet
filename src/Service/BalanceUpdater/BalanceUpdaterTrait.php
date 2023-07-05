<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
use App\Repository\AccountRepositoryInterface;
use Exception;

trait BalanceUpdaterTrait
{
    public function __construct(private int $previousId = 2)
    {
    }

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function compute(AccountRepositoryInterface $accountRepository, int $id): void
    {
        $selectedId = $this->getOlderRecordId($accountRepository, $id);

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
    public function setPreviousId(AccountRepositoryInterface $accountRepository, int $id): int
    {
        $input = $accountRepository->findAll();

        $reversed = array_reverse($input);
        foreach ($reversed as $key => $transaction) {
            $this->previousId = $transaction->getId();
            if (0 === $key && ($transaction->getId() === $id || ($reversed[$key + 1])->getId() === $id)) {
                throw new Exception("the first two records cannot be changed.... (for now)");
            } elseif (($reversed[$key + 1])->getId() === $id) {
                break;
            }
        }

        return $this->previousId;
    }

    private function getOlderRecordId(AccountRepositoryInterface $accountRepository, int $id): int
    {
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
}
