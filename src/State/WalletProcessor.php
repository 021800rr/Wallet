<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\WalletRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;

readonly class WalletProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface             $persistProcessor,
        private ProcessorInterface             $removeProcessor,
        private BalanceUpdaterFactoryInterface $walletFactory,
        private WalletRepositoryInterface      $walletRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->walletFactory->create()->compute($this->walletRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            $this->walletFactory->create()->compute($this->walletRepository, $data->getId());
        }
    }
}
