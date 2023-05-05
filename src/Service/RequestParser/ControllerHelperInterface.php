<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface ControllerHelperInterface
{
    /**
     * @param SymfonyRequest $request
     * @return int|array
     */
    public function process(SymfonyRequest $request): int|array;
}