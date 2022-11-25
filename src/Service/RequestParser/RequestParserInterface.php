<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request  as SymfonyRequest;

interface RequestParserInterface
{
    /**
     * @param string $controller
     * @param SymfonyRequest $request
     * @return int|array
     */
    public function strategy(string $controller, SymfonyRequest $request): int|array;
}
