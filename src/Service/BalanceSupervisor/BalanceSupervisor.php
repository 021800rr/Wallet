<?php

namespace App\Service\BalanceSupervisor;

use App\Entity\AbstractAccount;
use App\Entity\Chf;
use App\Entity\Wallet;
use App\Repository\AccountRepositoryInterface;
use Generator;

class BalanceSupervisor implements BalanceSupervisorInterface
{
    /** @var AbstractAccount[] */
    private array $supervisors;
    private float $initialBalance;

    /**
     * @param AbstractAccount[] $wallets
     * @return void
     */
    public function setWallets(array $wallets): void
    {
        $this->supervisors = $wallets;
        $this->initialBalance = $wallets[0]->getBalance();
    }

    public function crawl(AccountRepositoryInterface $accountRepository): Generator
    {
        $account = null;
        for ($step = 1; $step < count($this->supervisors); $step++) {
            /** @var Wallet|Chf $account */
            $account = $accountRepository->find($this->supervisors[$step]->getId());
            $balanceSupervisorBefore = $account->getBalanceSupervisor();
            $checker = (round($this->initialBalance + $account->getAmount(), 2));
            if ($account->getBalance() !== $checker) {
                $account->setBalanceSupervisor($checker);
                yield($account);
                $accountRepository->save($account);
            } else {
                $account->setBalanceSupervisor(null);
            }
            $balanceSupervisorAfter = $account->getBalanceSupervisor();
            if ($balanceSupervisorBefore !== $balanceSupervisorAfter) {
                $accountRepository->save($account);
            }
            $this->initialBalance = $account->getBalance();
        }
        if ($account) {
            $accountRepository->save($account, true);
        }
    }
}
