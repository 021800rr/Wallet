<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

readonly class RequestParser implements RequestParserInterface
{
    public function __construct(
        private ControllerHelperInterface $searchHelper,
        private ControllerHelperInterface $plnHelper,
    ) {
    }

    /**
     * @return array<int, int|string>
     */
    public function strategy(string $fullyQualifiedControllerName, SymfonyRequest $request): array
    {
        return "App\\Controller\\SearchController" === $fullyQualifiedControllerName
            ? $this->searchHelper->process($request)
            : $this->plnHelper->process($request);
    }
}
