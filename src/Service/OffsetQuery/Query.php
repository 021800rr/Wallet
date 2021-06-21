<?php

namespace App\Service\OffsetQuery;

class Query extends AbstractSession implements QueryInterface
{
    public function set(string $query)
    {
        if (!empty($query)) {
            $this->session->set('query', $query);
        }
    }

    public function get(): string
    {
        return $this->session->get('query', '');
    }

    public function reset()
    {
        $this->session->set('query', '');
    }
}
