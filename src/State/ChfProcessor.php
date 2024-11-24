<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Chf;
use App\Repository\AccountRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * @implements ProcessorInterface<Chf, void>
 */
readonly class ChfProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<Chf, Chf> $persistProcessor
     * @param ProcessorInterface<Chf, void> $removeProcessor
     */
    public function __construct(
        private BalanceUpdaterAccountInterface $walletUpdater,
        private ProcessorInterface             $persistProcessor,
        private ProcessorInterface             $removeProcessor,
        private AccountRepositoryInterface     $chfRepository,
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
        /** @var Chf $data */
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->walletUpdater->setPreviousId($this->chfRepository, $data->getId());
            $this->walletUpdater->compute($this->chfRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            if ($operation instanceof Patch || $operation instanceof Put) {
                /** @var int $id */
                $id = $uriVariables['id'];
                $this->walletUpdater->setPreviousId($this->chfRepository, $id);
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            } elseif ($operation instanceof Post) {
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                $this->walletUpdater->setPreviousId($this->chfRepository, $data->getId());
            }
            /** @var int $id */
            $id = $data->getId() ?? $id ?? throw new \Exception('Id is required');
            $this->walletUpdater->compute($this->chfRepository, $id);
        }
    }
}
