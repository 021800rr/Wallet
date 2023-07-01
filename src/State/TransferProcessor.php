<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Backup;
use App\Entity\TransferToBackup;
use App\Entity\TransferToWallet;
use App\Entity\Wallet;
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
     * @param array<mixed, mixed> $uriVariables
     * @param array<mixed, mixed> $context
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $contractor = $this->contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');
        if ($data instanceof TransferToBackup) {
            $backup = new Backup();
            $backup->setContractor($contractor);
            $backup->setAmount($data->getAmount());
            $backup->setDate($data->getDate());
            $this->agent->moveToBackup($backup, (int) $data->isCurrency());
        } else {
            $wallet = new Wallet();
            $wallet->setContractor($contractor);
            /** @var TransferToWallet $data */
            $wallet->setAmount($data->getAmount());
            $wallet->setDate($data->getDate());
            $this->agent->moveToWallet($wallet);
        }
    }
}
