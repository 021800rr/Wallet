<?php

namespace App\Service\RequestParser;

use App\Service\OffsetQuery\OffsetInterface;
use App\Service\OffsetQuery\QueryInterface;

abstract class AbstractParser
{
    protected OffsetInterface $offset;
    protected QueryInterface $query;

    public function __construct(OffsetInterface $offset, QueryInterface $query)
    {
        $this->offset = $offset;
        $this->query = $query;
    }
}
