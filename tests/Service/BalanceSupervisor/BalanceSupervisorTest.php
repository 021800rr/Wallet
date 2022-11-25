<?php

namespace App\Tests\Service\BalanceSupervisor;

use App\Service\BalanceSupervisor\BalanceSupervisor;
use App\Tests\Service\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BalanceSupervisorTest extends KernelTestCase
{
    use SetUp;

    public function testWalletCrawl(): void
    {
        $balanceSupervisor = new BalanceSupervisor($this->entityManager);
        $balanceSupervisor->setWallets($this->wallets);

        $generator = $balanceSupervisor->crawl($this->walletRepository);
        $result = [];
        foreach($generator as $wallet) {
            $result[] = $wallet->__toString();
        }

        $this->assertSame(
            ["2 : 2021-05-12 : -10 : 191 : 190 : Allegro", "3 : 2021-05-13 : -20 : 170 : 171 : Allegro"],
            $result
        );
    }

    public function testChfCrawl(): void
    {
        $balanceSupervisor = new BalanceSupervisor($this->entityManager);
        $balanceSupervisor->setWallets($this->chfs);

        $generator = $balanceSupervisor->crawl($this->chfRepository);
        $result = [];
        foreach($generator as $wallet) {
            $result[] = $wallet->__toString();
        }

        $this->assertSame(
            [],
            $result
        );
    }
}
