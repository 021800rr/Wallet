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

readonly class TransferProcessor implements ProcessorInterface
{
    public function __construct(
        private TransferInterface $agent,
        private ContractorRepository $contractorRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof TransferToBackup) {
            $backup = new Backup();
            $backup->setContractor($this->contractorRepository->getInternalTransferOwner());
            $backup->setAmount($data->getAmount());
            $backup->setDate($data->getDate());
            $this->agent->moveToBackup($backup, (int) $data->isCurrency());
        } else {
            $wallet = new Wallet();
            $wallet->setContractor($this->contractorRepository->getInternalTransferOwner());
            /** @var TransferToWallet $data */
            $wallet->setAmount($data->getAmount());
            $wallet->setDate($data->getDate());
            $this->agent->moveToWallet($wallet);
        }
    }
}
