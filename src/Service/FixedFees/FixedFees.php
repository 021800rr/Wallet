<?php

namespace App\Service\FixedFees;

use App\Entity\Fee;
use App\Entity\Wallet;
use App\Repository\FeeRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class FixedFees implements FixedFeesInterface
{
    private FeeRepository $feeRepository;
    private EntityManagerInterface $entityManager;
    private BalanceUpdaterInterface $walletUpdater;
    private WalletRepository $walletRepository;

    public function __construct(
        EntityManagerInterface  $entityManage,
        FeeRepository           $feeRepository,
        BalanceUpdaterInterface $walletUpdater,
        WalletRepository        $walletRepository
    ) {
        $this->entityManager = $entityManage;
        $this->feeRepository = $feeRepository;
        $this->walletUpdater = $walletUpdater;
        $this->walletRepository = $walletRepository;
    }

    /** @throws Exception */
    public function insert(): void
    {
        foreach ($this->feeRepository->findAll() as $fee) {
            $wallet = new Wallet();

            $wallet->setDate($this->getDate($fee));
            $wallet->setAmount($fee->getAmount());
            $wallet->setContractor($fee->getContractor());

            $this->entityManager->persist($wallet);
            $this->entityManager->flush();
            $this->walletUpdater->compute($this->walletRepository, $wallet->getId());
        }
    }

    /** @throws Exception */
    private function getDate(Fee $fee): DateTime
    {
        $date = new DateTime('now');
        $date->modify('first day of next month');
        $interval = 'P' . ($fee->getDate() - 1) . 'D';

        return $date->add(new DateInterval($interval));
    }
}
