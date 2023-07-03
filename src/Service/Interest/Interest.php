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

        /** @var DateTimeInterface $date */
        $date = $dataArray['date'];

        /** @var Contractor $contractor */
        $contractor = $dataArray['contractor'];

        /** @var float $retiring */
        $retiring = $dataArray['retiring'];

        /** @var float $holiday */
        $holiday = $dataArray['holiday'];

        /** @var float $amount */
        $amount = $dataArray['amount'];

        /** @var float $balance */
        $balance = $dataArray['balance'];

        $backup->setDate($date);
        $backup->setRetiring($retiring);
        $backup->setHoliday($holiday);
        $backup->setContractor($contractor);
        $backup->setAmount($amount);
        $backup->setBalance($balance);
        $backup->setInterest((bool) $dataArray['interest']);

        return $backup;
    }

    /**
     * @param FormInterface $form
     * @return array<string, DateTimeInterface|float|Contractor|bool|null>
     */
    private function simplifyFormArray(FormInterface $form): array
    {
        /** @var array<string, DateTimeInterface|float|Contractor|bool|null> $formArray */
        $formArray = $form->getData();

        $formArray['contractor'] = $this->contractorRepository->getInternalTransferOwner();
        $formArray['interest'] = false;

        /** @var float $retiring */
        $retiring = $formArray['retiring'];

        /** @var float $retiringTax */
        $retiringTax = $formArray['retiring_tax'];

        /** @var float $holiday */
        $holiday = $formArray['holiday'];

        /** @var float $holidayTax */
        $holidayTax = $formArray['holiday_tax'];

        $formArray['retiring'] = $retiring - $retiringTax;
        $formArray['holiday'] = $holiday - $holidayTax;

        $formArray['amount'] = $formArray['retiring'] + $formArray['holiday'];
        $formArray['balance'] = $formArray['amount'];

        return $formArray;
    }
}
