<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class SearchControllerHelper extends AbstractParser implements ControllerHelperInterface
{
    /**
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
