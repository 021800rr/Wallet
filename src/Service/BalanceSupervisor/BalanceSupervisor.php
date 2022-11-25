<?php

namespace App\Service\BalanceSupervisor;

use App\Entity\Chf;
use App\Entity\Wallet;
use App\Repository\AccountRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;

class BalanceSupervisor implements BalanceSupervisorInterface
{
    /** @var Wallet[]|Chf[] */
    private array $supervisors;
    private float $initialBalance;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Wallet[]|Chf[] $wallets
     * @return void
     */
    public function setWallets(array $wallets): void
    {
        $this->supervisors = $wallets;
        $this->initialBalance = $wallets[0]->getBalance();
    }

    public function crawl(AccountRepositoryInterface $accountRepository): Generator
    {
        for ($step = 1; $step < count($this->supervisors); $step++) {
            /** @var Wallet|Chf $account */
            $account = $accountRepository->find($this->supervisors[$step]->getId());
            $balanceSupervisorBefore = $account->getBalanceSupervisor();
            $checker = (round($this->initialBalance + $account->getAmount(), 2));
            if ($account->getBalance() !== $checker) {
                $account->setBalanceSupervisor($checker);
                yield($account);
                $this->entityManager->persist($account);
            } else {
                $account->setBalanceSupervisor(null);
            }
            $balanceSupervisorAfter = $account->getBalanceSupervisor();
            if ($balanceSupervisorBefore !== $balanceSupervisorAfter) {
                $this->entityManager->persist($account);
            }
            $this->initialBalance = $account->getBalance();
        }
        $this->entityManager->flush();
    }
}
