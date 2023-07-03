<?php

namespace App\Service\ExpectedBackup;

use DateTime;
use Exception;

class Calculator implements CalculatorInterface
{
    // array $backups
    // [
    //      [
    //          'yearMonth' => string 2021-06,
    //          'sum_of_amounts' => float 300,
    //      ],
    //      [
    //          'yearMonth' => string 2021-05,
    //          'sum_of_amounts' => float 300,
    //      ],
    // ]
    /**
     * @param array<int, array<string, string|float>> $backups
     * @return float
     * @throws Exception
     */
    public function compute(array $backups): float
    {
        if (empty($backups)) {
            throw new Exception('no backups');
        }

        $backup['yearMonth'] = '1970-01';
        $sum = 0;
        foreach ($backups as $backup) {
            $sum += $backup['sum_of_amounts'];
        }

        $firstDate = $this->foretellDate((string) $backup['yearMonth']);
        $lastDate = $this->foretellDate((string) $backups[0]['yearMonth']);

        $months = $this->computeNumberOfMonth($firstDate, $lastDate);

        return $sum / $months;
    }

    /**
     * @throws Exception
     */
    private function foretellDate(string $date): DateTime
    {
        $date .= '-01';

        return new DateTime($date);
    }

    private function computeNumberOfMonth(DateTime $firstDate, DateTime $lastDate): int
    {
        /** @var int $firstYear */
        $firstYear = $firstDate->format('Y');

        /** @var int $lastYear */
        $lastYear = $lastDate->format('Y');

        /** @var int $firstMonth */
        $firstMonth = $firstDate->format('m');

        /** @var int $lastMonth */
        $lastMonth = $lastDate->format('m');

        return (($lastYear - $firstYear) * 12) + ($lastMonth - $firstMonth) + 1;
    }
}
