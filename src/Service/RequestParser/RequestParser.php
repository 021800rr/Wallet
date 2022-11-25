<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RequestParser implements RequestParserInterface
{
    public function __construct(
        private readonly ControllerHelperInterface $searchHelper,
        private readonly ControllerHelperInterface $walletHelper
    ) {
    }

    /**
     * @param string $controller
     * @param SymfonyRequest $request
     * @return int|array
     */
    public function strategy(string $controller, SymfonyRequest $request): int|array
    {
        return match ($controller) {
            "App\\Controller\\SearchController" => $this->searchHelper->process($request),
            "App\\Controller\\WalletController" => $this->walletHelper->process($request),
        };
    }
}
