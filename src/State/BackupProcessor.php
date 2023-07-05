<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Backup;
use App\Repository\BackupRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use App\Service\BalanceUpdater\BalanceUpdaterFactoryInterface;
use Exception;

class BackupProcessor implements ProcessorInterface
{
    public function __construct(
        BalanceUpdaterFactoryInterface             $backupFactory,
        private BalanceUpdaterAccountInterface     $backupUpdater,
        private readonly ProcessorInterface        $persistProcessor,
        private readonly ProcessorInterface        $removeProcessor,
        private readonly BackupRepositoryInterface $backupRepository,
    ) {
        $this->backupUpdater = $backupFactory->create();
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
            $this->backupUpdater->setPreviousId($this->backupRepository, $data->getId());
            $this->backupUpdater->compute($this->backupRepository, $data->getId());
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        } else {
            if ($data->getId()) {
                $this->backupUpdater->setPreviousId($this->backupRepository, $data->getId());
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            } else {
                $this->persistProcessor->process($data, $operation, $uriVariables, $context);
                $this->backupUpdater->setPreviousId($this->backupRepository, $data->getId());
            }
            $this->backupUpdater->compute($this->backupRepository, $data->getId());
        }
    }
}
