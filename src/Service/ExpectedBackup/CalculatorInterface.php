<?php

namespace App\Service\ExpectedBackup;

interface CalculatorInterface
{
    public function compute(array $backups): float;
}
