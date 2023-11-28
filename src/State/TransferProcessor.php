<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Backup;
use App\Entity\TransferToBackup;
use App\Entity\TransferToPln;
use App\Entity\Pln;
use App\Repository\ContractorRepository;
use App\Service\Transfer\TransferInterface;
use Exception;

readonly class TransferProcessor implements ProcessorInterface
{
    public function __construct(
        private TransferInterface $agent,
        private ContractorRepository $contractorRepository,
    ) {
    }

    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $internalTransferOwner = $this->contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
        if ($data instanceof TransferToBackup) {
            $backup = new Backup();
            $backup->setContractor($internalTransferOwner);
            $backup->setAmount($data->getAmount());
            $backup->setDate($data->getDate());
            $this->agent->moveToBackup($backup, (int) $data->isCurrency());
        } else {
            $pln = new Pln();
            $pln->setContractor($internalTransferOwner);
            /** @var TransferToPln $data */
            $pln->setAmount($data->getAmount());
            $pln->setDate($data->getDate());
            $this->agent->moveToPln($pln);
        }
    }
}
