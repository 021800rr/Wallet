<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class WalletHelper extends AbstractParser implements ControllerHelperInterface
{
    public function process(SymfonyRequest $request): int
    {
        $this->queryHelper->resetQuery();
        $this->offsetHelper->setOffset($request);

        return $this->offsetHelper->getOffset($request);
    }
}
