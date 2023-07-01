<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\AbstractWallet;
use App\Entity\ChfChecker;
use App\Entity\WalletChecker;
use App\Repository\AccountRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;

abstract readonly class AbstractAccountCheckerProvider implements ProviderInterface
{
    public function __construct(
        protected BalanceSupervisorInterface $supervisor,
        protected WalletRepositoryInterface  $walletRepository,
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
        WalletChecker|ChfChecker $data,
    ): WalletChecker|ChfChecker {
        $supervisor->setWallets($accountRepository->getAllRecords());
        $generator = $supervisor->crawl($accountRepository);
        foreach ($generator as $account) {
            /** @var AbstractWallet $account */
            $data->addAccount($account);
        }
        if (empty($data->getAccounts())) {
            $data->setResult('Passed');
        }

        return $data;
    }
}
