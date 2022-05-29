<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface ControllerInterface
{
    /**
     * @param SymfonyRequest $request
     * @return int|array [string, int] $query, $offset
     */
    public function run(SymfonyRequest $request): int|array;
}
