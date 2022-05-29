<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request implements RequestInterface
{
    private ControllerInterface $search;
    private ControllerInterface $wallet;

    public function __construct(ControllerInterface $search, ControllerInterface $wallet)
    {
        $this->search = $search;
        $this->wallet = $wallet;
    }

    /**
     * @param string $controller
     * @param SymfonyRequest $request
     * @return int|array [string, int] $query, $offset
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
