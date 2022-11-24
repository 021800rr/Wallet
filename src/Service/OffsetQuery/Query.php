<?php

namespace App\Service\OffsetQuery;

class Query extends AbstractSession implements QueryInterface
{
    public function set(string $query): void
    {
        if (!empty($query)) {
            $this->requestStack->getSession()->set('query', $query);
        }
    }

    public function get(): string
    {
        return $this->requestStack->getSession()->get('query', '');
    }

    public function reset(): void
    {
        $this->requestStack->getSession()->set('query', '');
    }
}
