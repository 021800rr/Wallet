<?php

namespace App\Service\OffsetQuery;

use Symfony\Component\HttpFoundation\Request;

interface OffsetHelperInterface
{
    // set offset from valid request
    public function setOffset(Request $request): void;

    //get offset from remembered request
    public function getOffset(Request $request): int;

    // set offset to 0
    public function resetOffset(): void;
}
