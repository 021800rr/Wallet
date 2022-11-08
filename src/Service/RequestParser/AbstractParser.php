<?php

namespace App\Service\RequestParser;

use App\Service\OffsetQuery\OffsetInterface;
use App\Service\OffsetQuery\QueryInterface;

abstract class AbstractParser
{
    public function __construct(protected OffsetInterface $offset, protected QueryInterface $query)
    {
    }
}
