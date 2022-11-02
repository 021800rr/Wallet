<?php

namespace App\Service;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;

class BalanceSupervisor
{
    /** @var Wallet[]  */
    private array $wallets;
    private float $initialBalance;
    private WalletRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(WalletRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
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
