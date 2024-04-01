<?php

namespace App\Service\ExpectedBackup;

interface CalculatorInterface
{
    /**
     * @param array<int, array<string, string|float>> $backups
     */
    public function compute(array $backups): float;
}
