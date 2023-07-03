<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Pln;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;

class PlnProcessor implements ProcessorInterface
{
    public function __construct(
        BalanceUpdaterFactoryInterface              $walletFactory,
        private BalanceUpdaterAccountInterface      $walletUpdater,
        private readonly ProcessorInterface         $persistProcessor,
        private readonly ProcessorInterface         $removeProcessor,
        private readonly AccountRepositoryInterface $plnRepository,
    ) {
        $this->walletUpdater = $walletFactory->create();
    }

    /**
     * @param array<mixed, mixed> $uriVariables
     * @param array<mixed, mixed> $context
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var Pln $data */
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->walletUpdater->compute($this->plnRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            $this->walletUpdater->compute($this->plnRepository, $data->getId());
        }
    }
}
