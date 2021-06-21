<?php

namespace App\Service\OffsetQuery;

use Symfony\Component\HttpFoundation\Request;

interface OffsetInterface
{
    // set offset from valid request
    public function set(Request $request);

    //get offset from remembered request
    public function get(Request $request): int;

    // set offset to 0
    public function reset();
}
