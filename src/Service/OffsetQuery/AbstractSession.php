<?php

namespace App\Service\OffsetQuery;

use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractSession
{
    public function __construct(protected readonly RequestStack $requestStack)
    {
    }
}
