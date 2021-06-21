<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request  as SymfonyRequest;

interface RequestInterface
{
    /**
     * @param string $controller
     * @param SymfonyRequest $request
     * @return int|array [string, int] $query, $offset
     */
    public function strategy(string $controller, SymfonyRequest $request);
}
