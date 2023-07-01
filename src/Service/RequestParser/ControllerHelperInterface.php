<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface ControllerHelperInterface
{
    /**
     * @param SymfonyRequest $request
     * @return array<int, int|string>
     */
    public function process(SymfonyRequest $request): array;
}
