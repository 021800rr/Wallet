<?php

namespace App\Service\OffsetQuery;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class AbstractSession
{
    public function __construct(protected readonly RequestStack $requestStack)
    {
    }
}
