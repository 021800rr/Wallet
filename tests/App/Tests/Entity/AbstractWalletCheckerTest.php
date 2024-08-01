<?php

namespace App\Tests\Entity;

use App\Entity\AbstractWallet;
use App\Entity\AbstractWalletChecker;
use PHPUnit\Framework\TestCase;

class AbstractWalletCheckerTest extends TestCase
{
    public function testDefaultResult(): void
    {
        $walletChecker = new class() extends AbstractWalletChecker {};
        $this->assertEquals('Error', $walletChecker->getResult());
    }

    public function testSetResult(): void
    {
        $walletChecker = new class() extends AbstractWalletChecker {};
        $walletChecker->setResult('Success');
        $this->assertEquals('Success', $walletChecker->getResult());
    }

    public function testDefaultWallets(): void
    {
        $walletChecker = new class() extends AbstractWalletChecker {};
        $this->assertIsArray($walletChecker->getWallets());
        $this->assertCount(0, $walletChecker->getWallets());
    }

    public function testAddWallet(): void
    {
        $walletChecker = new class() extends AbstractWalletChecker {};
        $wallet = $this->createMock(AbstractWallet::class);
        $walletChecker->addWallet($wallet);

        $wallets = $walletChecker->getWallets();
        $this->assertCount(1, $wallets);
        $this->assertSame($wallet, $wallets[0]);
    }
}
