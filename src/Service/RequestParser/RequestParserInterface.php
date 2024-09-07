<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface RequestParserInterface
{
    /**
     * @return array<int, int|string>
     */
    public function strategy(string $fullyQualifiedControllerName, SymfonyRequest $request): array;
}
