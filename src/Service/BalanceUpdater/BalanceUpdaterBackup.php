<?php

namespace App\Service\BalanceUpdater;

use App\Entity\AbstractAccount;
use App\Entity\Backup;
use App\Repository\AccountRepositoryInterface;

class BalanceUpdaterBackup extends BalanceUpdaterAbstractAccount implements BalanceUpdaterAccountInterface
{
    use BalanceUpdaterTrait;

    /**
     * @param AccountRepositoryInterface $accountRepository
     * @param Backup $predecessor
     * @param Backup $transaction
     * @param Backup[]|null $successors
     * @return void
     */
    protected function walk(
        AccountRepositoryInterface $accountRepository,
        AbstractAccount            $predecessor,
        AbstractAccount            $transaction,
        ?array                     $successors,
    ): void {
        if (Backup::INAPPLICABLE === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
            $transaction = $this->setSubWallets($predecessor, $transaction);
        } elseif (Backup::NOT_PROCESSED === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getBalance());
            $transaction->setRetiring($predecessor->getRetiring() + $transaction->getRetiring());
            $transaction->setHoliday($predecessor->getHoliday() + $transaction->getHoliday());
            $transaction->setInterest(Backup::DONE);
        } elseif (Backup::DONE === $transaction->getInterest()) {
            $transaction->setBalance($predecessor->getBalance() + $transaction->getAmount());
        }
        $accountRepository->save($transaction, true);

        if (count($successors)) {
            $predecessor = $transaction;
            $transaction = array_shift($successors);
            $this->walk($accountRepository, $predecessor, $transaction, $successors);
        }
    }

    private function setSubWallets(Backup $predecessor, Backup $transaction): Backup
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
