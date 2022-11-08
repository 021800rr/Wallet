<?php

namespace App\Service\RequestParser;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Search extends AbstractParser implements ControllerInterface
{
    /**
     * @param SymfonyRequest $request
     * @return array
     */
    public function run(SymfonyRequest $request): array
    {
        $query = $this->query->get();
        $this->offset->set($request);
        $offset = $this->offset->get($request);

        return [$query, $offset];
    }
}
