<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

readonly class RequestParser implements RequestParserInterface
{
    public function __construct(
        private ControllerHelperInterface $searchHelper,
        private ControllerHelperInterface $walletHelper
    ) {
    }

    /**
     * @param string $fullyQualifiedControllerName
     * @param SymfonyRequest $request
     * @return int|array<int, int|string>
     */
    public function strategy(string $fullyQualifiedControllerName, SymfonyRequest $request): int|array
    {
        return "App\\Controller\\SearchController" === $fullyQualifiedControllerName
            ? $this->searchHelper->process($request)
            : $this->walletHelper->process($request);
    }
}
