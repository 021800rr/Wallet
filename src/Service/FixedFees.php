<?php

namespace App\Service;

use App\Entity\Fee;
use App\Entity\Wallet;
use App\Repository\FeeRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class FixedFees
{
    private FeeRepository $feeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManage, FeeRepository $feeRepository)
    {
        $this->entityManager = $entityManage;
        $this->feeRepository = $feeRepository;
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
