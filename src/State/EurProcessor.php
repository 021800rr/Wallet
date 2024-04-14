<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Eur;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

readonly class EurProcessor implements ProcessorInterface
{
    public function __construct(
        private BalanceUpdaterAccountInterface $walletUpdater,
        private ProcessorInterface             $persistProcessor,
        private ProcessorInterface             $removeProcessor,
        private AccountRepositoryInterface     $eurRepository,
    ) {
    }

    /**
     * @param mixed[] $uriVariables
     * @param array{
     *      request?: Request,
     *      previous_data?: mixed,
     *      resource_class?: string,
     *      original_data?: mixed
     *  } $context
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var Eur $data */
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->walletUpdater->setPreviousId($this->eurRepository, $data->getId());
            $this->walletUpdater->compute($this->eurRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            if ($data->getId()) {
                $this->walletUpdater->setPreviousId($this->eurRepository, $data->getId());
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            } else {
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                $this->walletUpdater->setPreviousId($this->eurRepository, $data->getId());
            }
            $this->walletUpdater->compute($this->eurRepository, $data->getId());
        }
    }
}
