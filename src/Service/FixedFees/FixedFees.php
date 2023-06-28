<?php

namespace App\Service\FixedFees;

use App\Entity\Fee;
use App\Entity\Wallet;
use App\Repository\FeeRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use DateInterval;
use DateTime;
use Exception;

readonly class FixedFees implements FixedFeesInterface
{
    public function __construct(
        private FeeRepositoryInterface         $feeRepository,
        private BalanceUpdaterFactoryInterface $walletFactory,
        private WalletRepositoryInterface      $walletRepository,
    ) {
    }

    /** @throws Exception */
    public function insert(): void
    {
        foreach ($this->feeRepository->findAll() as $fee) {
            $wallet = new Wallet();

            $wallet->setDate($this->getDate($fee));
            $wallet->setAmount($fee->getAmount());
            $wallet->setContractor($fee->getContractor());

            $this->walletRepository->save($wallet, true);
            $this->walletFactory->create()->compute($this->walletRepository, $wallet->getId());
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
