<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Eur;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * @implements ProcessorInterface<Eur, void>
 */
readonly class EurProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<Eur, Eur> $persistProcessor
     * @param ProcessorInterface<Eur, void> $removeProcessor
     */
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
            if ($operation instanceof Patch || $operation instanceof Put) {
                /** @var int $id */
                $id = $uriVariables['id'];
                $this->walletUpdater->setPreviousId($this->eurRepository, $id);
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            } elseif ($operation instanceof Post) {
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                $this->walletUpdater->setPreviousId($this->eurRepository, $data->getId());
            }
            /** @var int $id */
            $id = $data->getId() ?? $id ?? throw new \Exception('Id is required');
            $this->walletUpdater->compute($this->eurRepository, $id);
        }
    }
}
