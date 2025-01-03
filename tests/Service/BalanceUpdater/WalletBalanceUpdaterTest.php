<?php

namespace App\Tests\Service\BalanceUpdater;

use DateTime;
use App\Entity\AbstractAccount;
use App\Entity\Pln;
use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WalletBalanceUpdaterTest extends KernelTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testCompute(): void
    {
        /** @var Pln[] $transactions */
        $transactions = $this->plnRepository->findAll();
        $this->assertSame(100.00, $transactions[0]->getBalance());

        $transaction = $transactions[1];
        $transaction->setAmount($transaction->getAmount() - 10);

        $this->walletUpdater->setPreviousId($this->plnRepository, $transaction->getId());
        $this->walletUpdater->compute($this->plnRepository, $transaction->getId());

        /** @var Pln[] $transactions */
        $transactions = $this->plnRepository->findAll();
        $this->assertSame(90.00, $transactions[0]->getBalance());
    }

    public function testSetPreviousIdWithInvalidRequest(): void
    {
        $this->expectExceptionMessage('the first two records cannot be changed.... (for now)');
        $this->walletUpdater->setPreviousId($this->plnRepository, 1);

        $this->expectExceptionMessage('the first two records cannot be changed.... (for now)');
        $this->walletUpdater->setPreviousId($this->plnRepository, 2);
    }

    public function testSetPreviousId(): void
    {
        for ($id = 3; $id <= 5; $id++) {
            $pId = $this->walletUpdater->setPreviousId($this->plnRepository, $id);
            $this->assertSame($id - 1, $pId);
        }
    }

    public function testSetPreviousIdWithSwitchedData(): void
    {
        $pln = $this->plnRepository->find(3);
        /** @var AbstractAccount $pln */
        $pln->setDate(new DateTime('2021-04-14'));
        $this->plnRepository->save($pln, true);

        $pId = $this->walletUpdater->setPreviousId($this->plnRepository, 3);
        $this->assertSame(4, $pId);
    }

    public function testSetPreviousIdWithSameData(): void
    {
        $pln = $this->plnRepository->find(3);
        /** @var AbstractAccount $pln */
        $pln->setDate(new DateTime('2021-04-13'));
        $this->plnRepository->save($pln, true);

        $pId = $this->walletUpdater->setPreviousId($this->plnRepository, 3);
        $this->assertSame(2, $pId);
    }
}
