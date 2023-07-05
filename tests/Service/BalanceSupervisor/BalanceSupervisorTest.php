<?php

namespace App\Tests\Service\BalanceSupervisor;

use App\Entity\Chf;
use App\Entity\Pln;
use App\Service\BalanceSupervisor\BalanceSupervisor;
use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BalanceSupervisorTest extends KernelTestCase
{
    use SetUp;

    public function testPlnCrawl(): void
    {
        $balanceSupervisor = new BalanceSupervisor();
        $balanceSupervisor->setWallets($this->plns);

        $generator = $balanceSupervisor->crawl($this->plnRepository);
        $result = [];
        /** @var Pln $pln */
        foreach($generator as $pln) {
            $result[] = $pln->__toString();
        }

        $this->assertSame(
            [],
            $result
        );
    }

    public function testChfCrawl(): void
    {
        $balanceSupervisor = new BalanceSupervisor();
        $balanceSupervisor->setWallets($this->chfs);

        $generator = $balanceSupervisor->crawl($this->chfRepository);
        $result = [];
        /** @var Chf $chf */
        foreach($generator as $chf) {
            $result[] = $chf->__toString();
        }

        $this->assertSame(
            [],
            $result
        );
    }
}
