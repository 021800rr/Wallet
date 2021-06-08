<?php

namespace App\Service\Tests;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Entity\Wallet;
use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use App\Service\BackupBalanceUpdater;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BackupBalanceUpdaterTest extends KernelTestCase
{
    private BackupRepository $backupRepository;

    /** @var Doctrine\ORM\EntityManager */
    private $entityManager;
    private array $sets = [];
    private BackupBalanceUpdater $backupBalanceUpdater;

    public function testMoveAssets(): void
    {
        // wallet ->bkp, bkp ->wallet
        foreach ($this->sets as $set) {
            /** @var Wallet|Backup  $fromWallet */
            $fromWallet = $set['from'];
            /** @var Backup|Wallet $toWallet */
            $toWallet = $set['to'];

            /**
             * @var BackupRepository|WalletRepository $fromWalletRepo
             * @var WalletRepository|BackupRepository $toWalletRepo
             */
            list($fromWalletRepo, $toWalletRepo) = $this->assertBeforeMoveAssets($fromWallet, $toWallet);

            $toWallet->setAmount(100);
            $this->backupBalanceUpdater->moveAssets($toWallet);

            $this->assertAfterMoveAssets($fromWalletRepo, $toWalletRepo, $toWallet);
        }
    }

    public function testCompute(): void
    {
        /** @var Backup[] $transactions */
        $transactions = $this->backupRepository->findAll();
        $this->assertSame(300.00, ($transactions[0])->getRetiring());
        $this->assertSame(300.00, ($transactions[0])->getHoliday());
        $this->assertSame(600.00, ($transactions[0])->getBalance());

        $transaction = $transactions[1];
        $this->assertSame(200.00, $transaction->getAmount());
        $transaction->setAmount($transaction->getAmount() - 10);
        $this->assertSame(190.00, $transaction->getAmount());

        $this->backupBalanceUpdater->compute($this->backupRepository);

        $transactions = $this->backupRepository->findAll();
        $this->assertSame(295.00, ($transactions[0])->getRetiring());
        $this->assertSame(295.00, ($transactions[0])->getHoliday());
        $this->assertSame(590.00, ($transactions[0])->getBalance());
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->backupRepository = $this->entityManager->getRepository(Backup::class);

        $this->backupBalanceUpdater = new BackupBalanceUpdater(
            $this->entityManager,
            $this->entityManager->getRepository(Wallet::class),
            $this->backupRepository,
            $this->entityManager->getRepository(Contractor::class)
        );

        $this->sets = [
            [
                'from' => new Wallet(),
                'to' => new Backup()
            ], [
                'from' => new Backup(),
                'to' => new Wallet()
            ]
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @param Wallet|Backup $fromWallet
     * @param Wallet|Backup $toWallet
     * @return array [BackupRepository|WalletRepository, WalletRepository|BackupRepository]
     */
    private function assertBeforeMoveAssets($fromWallet, $toWallet): array
    {
        /** @var BackupRepository|WalletRepository $fromWalletRepo */
        $fromWalletRepo = $this->entityManager->getRepository(get_class($fromWallet));
        /** @var WalletRepository|BackupRepository $toWalletRepo */
        $toWalletRepo = $this->entityManager->getRepository(get_class($toWallet));

        $oldFromWalletBalance = $fromWalletRepo->getCurrentBalance();
        $oldToWalletBalance = $toWalletRepo->getCurrentBalance();

        switch (get_class($toWallet)) {
            case "App\\Entity\\Backup": // before first transfer (wallet -> bkp)
                $this->assertSame(170.00, $oldFromWalletBalance);
                $this->assertSame(600.00, $oldToWalletBalance);
                break;
            case "App\\Entity\\Wallet": // before second transfer (bkp -> wallet)
                /** @var Backup[] $transactions */
                $transactions = $this->backupRepository->findAll();
                $this->assertSame(350.00, ($transactions[0])->getRetiring());
                $this->assertSame(350.00, ($transactions[0])->getHoliday());
                $this->assertSame(700.00, ($transactions[0])->getBalance());

                $this->assertSame(700.00, $oldFromWalletBalance);
                $this->assertSame(70.00, $oldToWalletBalance);
                break;
        }

        return [$fromWalletRepo, $toWalletRepo];
    }

    /**
     * @param BackupRepository|WalletRepository $fromWalletRepo
     * @param WalletRepository|BackupRepository $toWalletRepo
     * @param Wallet|Backup $toWallet
     */
    private function assertAfterMoveAssets($fromWalletRepo, $toWalletRepo, $toWallet): void
    {
        $newFromWalletBalance = $fromWalletRepo->getCurrentBalance();
        $newToWalletBalance = $toWalletRepo->getCurrentBalance();

        switch (get_class($toWallet)) {
            case "App\\Entity\\Backup": // after first transfer (wallet -> bkp)
                $this->assertSame(70.00, $newFromWalletBalance);
                $this->assertSame(700.00, $newToWalletBalance);
                break;
            case "App\\Entity\\Wallet": // after second transfer (bkp -> wallet)
                /** @var Backup[] $transactions */
                $transactions = $this->backupRepository->findAll();
                $this->assertSame(350.00, ($transactions[0])->getRetiring());
                $this->assertSame(250.00, ($transactions[0])->getHoliday());

                $this->assertSame(600.00, $newFromWalletBalance);
                $this->assertSame(170.00, $newToWalletBalance);
                break;
        }
    }
}
