<?php

namespace App\Service\ExpectedBackup;

interface CalculatorInterface
{
    /**
     * @param array<int, array<string, string|float>> $backups
     * @return float
     */
    public function compute(array $backups): float;
}
