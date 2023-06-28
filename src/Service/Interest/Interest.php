<?php

namespace App\Service\Interest;

use App\Entity\Backup;
use App\Entity\Contractor;
use App\Repository\ContractorRepositoryInterface;
use DateTimeInterface;
use Symfony\Component\Form\FormInterface;

readonly class Interest implements InterestInterface
{
    public function __construct(private ContractorRepositoryInterface $contractorRepository)
    {
    }

    public function form2Backup(FormInterface $form): Backup
    {
        $backup = new Backup();
        $dataArray = $this->simplifyFormArray($form);

        $backup->setDate($dataArray['date']);
        $backup->setRetiring($dataArray['retiring']);
        $backup->setHoliday($dataArray['holiday']);
        $backup->setContractor($dataArray['contractor']);
        $backup->setAmount($dataArray['amount']);
        $backup->setBalance($dataArray['balance']);
        $backup->setInterest($dataArray['interest']);

        return $backup;
    }

    /**
     * @param FormInterface $form
     * @return array<string, DateTimeInterface|float|Contractor|bool|null>
     */
    private function simplifyFormArray(FormInterface $form): array
    {
        $formArray = $form->getData();

        $formArray['contractor'] = $this->contractorRepository->getInternalTransferOwner();
        $formArray['interest'] = false;

        $formArray['retiring'] -= $formArray['retiring_tax'];
        $formArray['holiday'] -= $formArray['holiday_tax'];

        $formArray['amount'] = $formArray['retiring'] + $formArray['holiday'];
        $formArray['balance'] = $formArray['amount'];

        return $formArray;
    }
}
