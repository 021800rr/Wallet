<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Pln;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;

readonly class PlnProcessor implements ProcessorInterface
{
    public function __construct(
        private BalanceUpdaterAccountInterface $walletUpdater,
        private ProcessorInterface             $persistProcessor,
        private ProcessorInterface             $removeProcessor,
        private AccountRepositoryInterface     $plnRepository,
    ) {
    }

    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var Pln $data */
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->walletUpdater->setPreviousId($this->plnRepository, $data->getId());
            $this->walletUpdater->compute($this->plnRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            if ($data->getId()) {
                $this->walletUpdater->setPreviousId($this->plnRepository, $data->getId());
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            } else {
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                $this->walletUpdater->setPreviousId($this->plnRepository, $data->getId());
            }
            $this->walletUpdater->compute($this->plnRepository, $data->getId());
        }
    }
}
