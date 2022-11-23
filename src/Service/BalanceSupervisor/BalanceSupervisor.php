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

    public function crawl(AccountRepositoryInterface $repository): Generator
    {
        for ($step = 1; $step < count($this->supervisors); $step++) {
            /** @var Wallet|Chf $wallet */
            $wallet = $repository->find($this->supervisors[$step]->getId());
            $balanceSupervisorBefore = $wallet->getBalanceSupervisor();
            $checker = (round($this->initialBalance + $wallet->getAmount(), 2));
            if ($wallet->getBalance() !== $checker) {
                $wallet->setBalanceSupervisor($checker);
                yield($wallet);
                $this->entityManager->persist($wallet);
            } else {
                $wallet->setBalanceSupervisor(null);
            }
            $balanceSupervisorAfter = $wallet->getBalanceSupervisor();
            if ($balanceSupervisorBefore !== $balanceSupervisorAfter) {
                $this->entityManager->persist($wallet);
            }
            $this->initialBalance = $wallet->getBalance();
        }
        $this->entityManager->flush();
    }
}
