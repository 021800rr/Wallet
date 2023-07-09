<?php

namespace App\Service\FixedFees;

use App\Entity\Fee;
use App\Entity\Pln;
use App\Repository\FeeRepositoryInterface;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use DateInterval;
use DateTime;
use Exception;

readonly class FixedFees implements FixedFeesInterface
{
    public function __construct(
        private BalanceUpdaterAccountInterface $walletUpdater,
        private FeeRepositoryInterface         $feeRepository,
        private AccountRepositoryInterface     $plnRepository,
    ) {
    }

    /** @throws Exception */
    public function insert(): void
    {
        foreach ($this->feeRepository->findAll() as $fee) {
            $pln = new Pln();

            /** @var Fee $fee */
            $pln->setDate($this->getDate($fee));
            $pln->setAmount($fee->getAmount());
            $pln->setContractor($fee->getContractor());

            $this->plnRepository->save($pln, true);
            $this->walletUpdater->setPreviousId($this->plnRepository, $pln->getId());
            $this->walletUpdater->compute($this->plnRepository, $pln->getId());
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
