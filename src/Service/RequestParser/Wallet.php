<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Wallet extends AbstractParser implements ControllerInterface
{
    public function run(SymfonyRequest $request): int
    {
        $this->query->reset();
        $this->offset->set($request);

        return $this->offset->get($request);
    }
}
