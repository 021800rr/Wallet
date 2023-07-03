<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\AbstractWallet;
use App\Entity\ChfChecker;
use App\Entity\PlnChecker;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;

abstract readonly class AbstractWalletCheckerProvider implements ProviderInterface
{
    public function __construct(
        protected BalanceSupervisorInterface $supervisor,
        protected AccountRepositoryInterface $plnRepository,
        protected AccountRepositoryInterface $chfRepository,
    ) {
    }

    /**
     * @param array<mixed, mixed> $uriVariables
     * @param array<mixed, mixed> $context
     */
    abstract public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null;

    protected function accountChecker(
        BalanceSupervisorInterface $supervisor,
        AccountRepositoryInterface $accountRepository,
        PlnChecker|ChfChecker      $data,
    ): PlnChecker|ChfChecker {
        /** @var AbstractWallet[] $wallets */
        $wallets = $accountRepository->getAllRecords();
        $supervisor->setWallets($wallets);
        $generator = $supervisor->crawl($accountRepository);
        /** @var AbstractWallet $wallet */
        foreach ($generator as $wallet) {
            $data->addWallet($wallet);
        }
        if (empty($data->getWallets())) {
            $data->setResult('Passed');
        }

        return $data;
    }
}
