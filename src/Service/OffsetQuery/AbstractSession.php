<?php

namespace App\Service\OffsetQuery;

use Symfony\Component\HttpFoundation\RequestStack;

abstract readonly class AbstractSession
{
    public function __construct(protected RequestStack $requestStack)
    {
    }
}
