<?php

namespace App\Handler;

use App\Service\FixedFees\FixedFeesInterface;

class FeeHandler
{
    public function handle(FixedFeesInterface $fixedFees): void
    {
        $fixedFees->insert();
    }
}
