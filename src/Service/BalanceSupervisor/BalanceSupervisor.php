<?php

namespace App\Service\BalanceSupervisor;

use App\Entity\AbstractWallet;
use App\Entity\Chf;
use App\Entity\Pln;
use App\Repository\AccountRepositoryInterface;
use Generator;

class BalanceSupervisor implements BalanceSupervisorInterface
{
    /** @var AbstractWallet[] */
    private array $supervisors;
    private float $initialBalance;

    /**
     * @param AbstractWallet[] $wallets
     */
    public function setWallets(array $wallets): void
    {
        $this->supervisors = $wallets;
        $this->initialBalance = $wallets[0]->getBalance();
    }

    public function crawl(AccountRepositoryInterface $accountRepository): Generator
    {
        $wallet = null;
        $counter = count($this->supervisors);
        for ($step = 1; $step < $counter; $step++) {
            /** @var Pln|Chf $wallet */
            $wallet = $accountRepository->find($this->supervisors[$step]->getId());
            $balanceSupervisorBefore = $wallet->getBalanceSupervisor();
            $checker = (float) number_format(($this->initialBalance + $wallet->getAmount()), 2, '.', '');
            if ($wallet->getBalance() !== $checker) {
                $wallet->setBalanceSupervisor($checker);
                yield($wallet);
                $accountRepository->save($wallet);
            } else {
                $wallet->setBalanceSupervisor(null);
            }
            $balanceSupervisorAfter = $wallet->getBalanceSupervisor();
            if ($balanceSupervisorBefore !== $balanceSupervisorAfter) {
                $accountRepository->save($wallet);
            }
            $this->initialBalance = $wallet->getBalance();
        }
        if ($wallet) {
            $accountRepository->save($wallet, true);
        }
    }
}
