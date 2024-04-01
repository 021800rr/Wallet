<?php

namespace App\Service\OffsetQuery;

readonly class QueryHelper extends AbstractSession implements QueryHelperInterface
{
    public function setQuery(string $query): void
    {
        if ('' !== $query && '0' !== $query) {
            $this->requestStack->getSession()->set('query', $query);
        }
    }

    public function getQuery(): string
    {
        /** @var string */
        return $this->requestStack->getSession()->get('query', '');
    }

    public function resetQuery(): void
    {
        $this->requestStack->getSession()->set('query', '');
    }
}
