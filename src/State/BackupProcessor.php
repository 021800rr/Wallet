<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Backup;
use App\Repository\BackupRepositoryInterface;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * @implements ProcessorInterface<Backup, void>
 */
readonly class BackupProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<Backup, Backup> $persistProcessor
     * @param ProcessorInterface<Backup, void>   $removeProcessor
     */
    public function __construct(
        private BalanceUpdaterAccountInterface $backupUpdater,
        private ProcessorInterface             $persistProcessor,
        private ProcessorInterface             $removeProcessor,
        private BackupRepositoryInterface      $backupRepository,
    ) {
    }

    /**
     * @param mixed[] $uriVariables
     * @param array{
     *     request?: Request,
     *     previous_data?: mixed,
     *     resource_class?: string,
     *     original_data?: mixed
     * } $context
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
        } elseif ($operation instanceof Patch) {
            /** @var int $id */
            $id = $uriVariables['id'];
            $this->backupUpdater->setPreviousId($this->backupRepository, $id);
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            $this->backupUpdater->compute($this->backupRepository, $id);
        }
    }
}
