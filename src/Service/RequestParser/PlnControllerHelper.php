<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class PlnControllerHelper extends AbstractParser implements ControllerHelperInterface
{
    /**
     * @param SymfonyRequest $request
     * @return array<int, int|string>
     */
    public function process(SymfonyRequest $request): array
    {
        $this->queryHelper->resetQuery();
        $this->offsetHelper->setOffset($request);

        return ['', $this->offsetHelper->getOffset($request)];
    }
}
