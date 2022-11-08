<?php

namespace App\Service\FixedFees;

use App\Entity\Fee;
use App\Entity\Wallet;
use App\Repository\FeeRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class FixedFees implements FixedFeesInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FeeRepositoryInterface $fee,
        private readonly BalanceUpdaterInterface $walletUpdater,
        private readonly WalletRepositoryInterface $walletRepository
    ) {
    }

    /** @throws Exception */
    public function insert(): void
    {
        foreach ($this->fee->findAll() as $fee) {
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
