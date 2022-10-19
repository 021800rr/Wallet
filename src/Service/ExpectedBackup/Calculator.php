<?php

namespace App\Service\ExpectedBackup;

use DateTime;
use Exception;

class Calculator
{
    /**
     * @throws Exception
     */
    public function compute(array $backups): float
    {
        if (empty($backups)) {
            throw new Exception('no backups');
        }

        $sum = 0;
        foreach ($backups as $backup) {
            $sum += $backup['sa'];
        }

        $firstDate = $this->foretellDate($backup['yearMonth']);
        $lastDate = $this->foretellDate($backups[0]['yearMonth']);

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
        /** @var int $lastYear */
        /** @var int $firstMonth */
        /** @var int $lastMonth */

        $firstYear = $firstDate->format('Y');
        $lastYear = $lastDate->format('Y');

        $firstMonth = $firstDate->format('m');
        $lastMonth = $lastDate->format('m');

        return (($lastYear - $firstYear) * 12) + ($lastMonth - $firstMonth) + 1;
    }
}
