<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Backup;
use App\Repository\BackupRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;

readonly class BackupProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface             $persistProcessor,
        private ProcessorInterface             $removeProcessor,
        private BalanceUpdaterFactoryInterface $backupFactory,
        private BackupRepositoryInterface      $backupRepository,
    ) {
    }

    /**
     * @param array<mixed, mixed> $uriVariables
     * @param array<mixed, mixed> $context
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var Backup $data */
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->backupFactory->create()->compute($this->backupRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            $this->backupFactory->create()->compute($this->backupRepository, $data->getId());
        }
    }
}
