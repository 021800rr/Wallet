<?php

namespace App\Service\RequestParser;

use App\Service\OffsetQuery\OffsetHelperInterface;
use App\Service\OffsetQuery\QueryHelperInterface;

abstract class AbstractParser
{
    public function __construct(protected OffsetHelperInterface $offsetHelper, protected QueryHelperInterface $queryHelper)
    {
    }
}
