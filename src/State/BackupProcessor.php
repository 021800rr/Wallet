<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Backup;
use App\Repository\BackupRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;

readonly class BackupProcessor implements ProcessorInterface
{
    public function __construct(
        private BalanceUpdaterAccountInterface $backupUpdater,
        private ProcessorInterface             $persistProcessor,
        private ProcessorInterface             $removeProcessor,
        private BackupRepositoryInterface      $backupRepository,
    ) {
    }

    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
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
