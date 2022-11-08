<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request implements RequestInterface
{
    public function __construct(
        private readonly ControllerInterface $search,
        private readonly ControllerInterface $wallet
    ) {
    }

    /**
     * @param string $controller
     * @param SymfonyRequest $request
     * @return int|array
     */
    public function strategy(string $controller, SymfonyRequest $request): int|array
    {
        $result = null;
        switch ($controller) {
            case "App\\Controller\\SearchController":
                $result = $this->search->run($request);
                break;
            case "App\\Controller\\WalletController":
                $result = $this->wallet->run($request);
                break;
        }
        return $result;
    }
}
