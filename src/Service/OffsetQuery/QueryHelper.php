<?php

namespace App\Service\OffsetQuery;

readonly class QueryHelper extends AbstractSession implements QueryHelperInterface
{
    public function setQuery(string $query): void
    {
        if (!empty($query)) {
            $this->requestStack->getSession()->set('query', $query);
        }
    }

    public function getQuery(): string
    {
        return $this->requestStack->getSession()->get('query', '');
    }

    public function resetQuery(): void
    {
        $this->requestStack->getSession()->set('query', '');
    }
}
