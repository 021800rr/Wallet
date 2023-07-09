<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
use App\Entity\Backup;
use App\Repository\AccountRepositoryInterface;

class BalanceUpdaterBackup extends BalanceUpdaterAbstractAccount implements BalanceUpdaterAccountInterface
{
    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param AbstractAccount $predecessor
     * @param AbstractAccount $transaction
     * @param AbstractAccount[]|null $successors
     * @return void
     */
    protected function walk(
        AccountRepositoryInterface $accountRepository,
        AbstractAccount            $predecessor,
        AbstractAccount            $transaction,
        ?array                     $successors,
    ): void {
        /** @var Backup $transaction */
        /** @var Backup $predecessor */
        if (Backup::INAPPLICABLE === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
            $transaction = $this->setSubAccounts($predecessor, $transaction);
        } elseif (Backup::NOT_PROCESSED === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getBalance());
            $transaction->setRetiring($predecessor->getRetiring() + $transaction->getRetiring());
            $transaction->setHoliday($predecessor->getHoliday() + $transaction->getHoliday());
            $transaction->setInterest(Backup::DONE);
        } elseif (Backup::DONE === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        }
        $accountRepository->save($transaction, true);

        /** @var array<int, Backup> $successors */
        if (count($successors)) {
            $predecessor = $transaction;
            $transaction = array_shift($successors);
            $this->walk($accountRepository, $predecessor, $transaction, $successors);
        }
    }

    private function setSubAccounts(Backup $predecessor, Backup $transaction): Backup
    {
        if (0.0 < $transaction->getAmount()) {
            $transaction->setRetiring($predecessor->getRetiring() + $transaction->getAmount() / 2);
            $transaction->setHoliday($predecessor->getHoliday() + $transaction->getAmount() / 2);
        } else {
            $transaction->setRetiring($predecessor->getRetiring());
            $transaction->setHoliday(
                $predecessor->getHoliday() + $transaction->getAmount()
            );
        }

        return $transaction;
    }
}
