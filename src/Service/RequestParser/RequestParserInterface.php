<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request  as SymfonyRequest;

interface RequestParserInterface
{
    /**
     * @param string $fullyQualifiedControllerName
     * @param SymfonyRequest $request
     * @return array<int, int|string>
     */
    public function strategy(string $fullyQualifiedControllerName, SymfonyRequest $request): array;
}
