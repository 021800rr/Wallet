<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ChfChecker;
use App\Entity\WalletChecker;
use App\Repository\AccountRepositoryInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceSupervisor\BalanceSupervisorInterface;

abstract class AbstractAccountCheckerProvider implements ProviderInterface
{
    public function __construct(
        protected readonly BalanceSupervisorInterface $supervisor,
        protected readonly WalletRepositoryInterface  $walletRepository,
        protected readonly AccountRepositoryInterface $chfRepository,
    ) {
    }

    abstract public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null;

    protected function accountChecker(
        BalanceSupervisorInterface $supervisor,
        AccountRepositoryInterface $accountRepository,
        WalletChecker|ChfChecker $data,
    ): WalletChecker|ChfChecker {
        $supervisor->setWallets($accountRepository->getAllRecords());
        $generator = $supervisor->crawl($accountRepository);
        foreach ($generator as $account) {
            $data->addAccount($account);
        }
        if (empty($data->getAccounts())) {
            $data->setResult('Passed');
        }

        return $data;
    }
}
