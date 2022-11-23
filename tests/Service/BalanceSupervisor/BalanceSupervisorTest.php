<?php

namespace App\Tests\Service\BalanceSupervisor;

use App\Entity\Chf;
use App\Entity\Wallet;
use App\Repository\ChfRepository;
use App\Repository\WalletRepository;
use App\Service\BalanceSupervisor\BalanceSupervisor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BalanceSupervisorTest extends KernelTestCase
{
    private $entityManager;

    private WalletRepository $repository;
    /** @var Wallet[] $wallets */
    private array $wallets;

    private ChfRepository $chfRepository;
    /** @var Chf[] $chfs */
    private array $chfs;

    public function testCrawl(): void
    {
        $balanceSupervisor = new BalanceSupervisor($this->entityManager);
        $balanceSupervisor->setWallets($this->wallets);

        $generator = $balanceSupervisor->crawl($this->repository);
        $result = [];
        foreach($generator as $wallet) {
            $result[] = $wallet->__toString();
        }

        $this->assertSame(
            ["2 : 2021-05-12 : -10 : 191 : 190 : Allegro", "3 : 2021-05-12 : -20 : 170 : 171 : Allegro"],
            $result
        );

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

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->repository = $this->entityManager->getRepository(Wallet::class);
        $this->wallets = $this->repository->getAllRecords();

        $this->chfRepository = $this->entityManager->getRepository(Chf::class);
        $this->chfs = $this->chfRepository->getAllRecords();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
