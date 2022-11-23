<?php

namespace App\Tests\Service\BalanceSupervisor;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use App\Service\BalanceSupervisor\BalanceSupervisor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BalanceSupervisorTest extends KernelTestCase
{
    private WalletRepository $repository;
    private $entityManager;
    /** @var Wallet[] $wallets */
    private array $wallets;

    public function testCrawl(): void
    {
        $balanceSupervisor = new BalanceSupervisor($this->repository, $this->entityManager);
        $balanceSupervisor->setWallets($this->wallets);

        $generator = $balanceSupervisor->crawl();
        $result = [];
        foreach($generator as $wallet) {
            $result[] = $wallet->__toString();
        }

        $this->assertSame(
            ["2 : 2021-05-12 : -10 : 191 : 190 : Allegro", "3 : 2021-05-12 : -20 : 170 : 171 : Allegro"],
            $result
        );
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Wallet::class);
        $this->wallets = $this->repository->getAllRecords();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
