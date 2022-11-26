<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\BackupRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterInterface;
use Exception;

class BackupProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ProcessorInterface        $persistProcessor,
        private readonly ProcessorInterface        $removeProcessor,
        private readonly BalanceUpdaterInterface   $backupUpdater,
        private readonly BackupRepositoryInterface $backupRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof DeleteOperationInterface) {
            $data->setAmount(0);
            $this->backupUpdater->compute($this->backupRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            $this->backupUpdater->compute($this->backupRepository, $data->getId());
        }
    }
}
