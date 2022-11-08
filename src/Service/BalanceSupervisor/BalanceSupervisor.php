<?php

namespace App\Service\BalanceSupervisor;

use App\Entity\Wallet;
use App\Repository\WalletRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;

class BalanceSupervisor implements BalanceSupervisorInterface
{
    /** @var Wallet[]  */
    private array $wallets;
    private float $initialBalance;

    public function __construct(
        private readonly WalletRepositoryInterface $repository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param Wallet[] $wallets
     * @return void
     */
    public function setWallets(array $wallets): void
    {
        $this->wallets = $wallets;
        $initialWallet = $wallets[0];
        $this->initialBalance = $initialWallet->getBalance();
    }

    public function crawl(): Generator
    {
        $caught = false;
        $lastBalance = $this->initialBalance;
        for ($step = 1; $step < count($this->wallets); $step++) {
            $supervisorWallet = $this->wallets[$step];
            $wallet = $this->repository->find($supervisorWallet->getId());
            $lastBalanceSupervisor = $wallet->getBalanceSupervisor();
            $supervisorWallet->setBalanceSupervisor(round($lastBalance + $wallet->getAmount(), 2));
            if ($wallet->getBalance() !== $supervisorWallet->getBalanceSupervisor()) {
                $wallet->setBalanceSupervisor($supervisorWallet->getBalanceSupervisor());
                yield($wallet);
            } else {
                $wallet->setBalanceSupervisor(null);
            }
            if ($lastBalanceSupervisor !== $wallet->getBalanceSupervisor()) {
                $this->entityManager->persist($wallet);
                $caught = true;
            }
            $lastBalance = $wallet->getBalance();
        }
        if ($caught) {
            $this->entityManager->flush();
        }
    }
}
