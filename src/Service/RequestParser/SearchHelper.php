<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class SearchHelper extends AbstractParser implements ControllerHelperInterface
{
    /**
     * @param SymfonyRequest $request
     * @return array<int, int|string>
     */
    public function process(SymfonyRequest $request): array
    {
        $query = $this->queryHelper->getQuery();

        $this->offsetHelper->setOffset($request);
        $offset = $this->offsetHelper->getOffset($request);

        return [$query, $offset];
    }
}
