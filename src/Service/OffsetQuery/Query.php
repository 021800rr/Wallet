<?php

namespace App\Service\OffsetQuery;

class Query extends AbstractSession implements QueryInterface
{
    public function set(string $query)
    {
        if (!empty($query)) {
            $this->requestStack->getSession()->set('query', $query);
        }
    }

    public function get(): string
    {
        return $this->requestStack->getSession()->get('query', '');
    }

    public function reset()
    {
        $this->requestStack->getSession()->set('query', '');
    }
}
