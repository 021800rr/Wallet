<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Chf;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;

class ChfProcessor implements ProcessorInterface
{
    public function __construct(
        BalanceUpdaterFactoryInterface              $walletFactory,
        private BalanceUpdaterAccountInterface      $walletUpdater,
        private readonly ProcessorInterface         $persistProcessor,
        private readonly ProcessorInterface         $removeProcessor,
        private readonly AccountRepositoryInterface $chfRepository,
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
        /** @var Chf $data */
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->walletUpdater->setPreviousId($this->chfRepository, $data->getId());
            $this->walletUpdater->compute($this->chfRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            if ($data->getId()) {
                $this->walletUpdater->setPreviousId($this->chfRepository, $data->getId());
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            } else {
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                $this->walletUpdater->setPreviousId($this->chfRepository, $data->getId());
            }
            $this->walletUpdater->compute($this->chfRepository, $data->getId());
        }
    }
}
