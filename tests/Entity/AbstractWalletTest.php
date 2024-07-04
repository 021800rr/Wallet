<?php

namespace App\Tests\Entity;

use App\Entity\AbstractWallet;
use App\Entity\Contractor;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractWalletTest extends KernelTestCase
{
    public function testToStringMethod(): void
    {
        $contractor = new Contractor();
        $contractor->setDescription("Test Contractor");

        $wallet = new class() extends AbstractWallet {};
        $wallet->setDate(new DateTime('2023-01-01'));
        $wallet->setAmount(100.00);
        $wallet->setBalance(100.00);
        $wallet->setBalanceSupervisor(100.00);
        $wallet->setIsConsistent(true);
        $wallet->setContractor($contractor);
        $wallet->setDescription("Test Description");

        $expectedString = $wallet->getId() . ' : ' .
            $wallet->getDate()->format('Y-m-d') . ' : ' .
            $wallet->getAmount() . ' : ' .
            $wallet->getBalance() . ' : ' .
            $wallet->getBalanceSupervisor() . ' : ' .
            $wallet->getContractor()?->getDescription() .
            ' : Test Description';

        $this->assertEquals($expectedString, (string)$wallet);
    }
}
