<?php

namespace App\Tests\Service\FixedFees;

use App\Entity\Pln;
use App\Service\BalanceUpdater\BalanceUpdaterWallet;
use App\Service\FixedFees\FixedFees;
use App\Tests\SetUp;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FixedFeesTest extends KernelTestCase
{
    use SetUp;

    /**
     * @throws Exception
     */
    public function testInsert(): void
    {
        $fixedFees = new FixedFees(
            $this->walletFactory,
            new BalanceUpdaterWallet(),
            $this->feeRepository,
            $this->plnRepository,
        );

        /** @var Pln[] $transactions */
        $transactions = $this->plnRepository->findAll();
        $this->assertSame(170.00, $transactions[0]->getBalance());

        $fixedFees->insert();

        /** @var Pln[] $transactions */
        $transactions = $this->plnRepository->findAll();
        $this->assertSame(98.01, $transactions[0]->getBalance());
    }
}
