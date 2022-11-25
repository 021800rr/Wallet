<?php

namespace App\Service\OffsetQuery;

interface QueryHelperInterface
{
    // set query from last valid post
    public function setQuery(string $query): void;

    // get query from last remembered post
    public function getQuery(): string;

    // set query to empty string
    public function resetQuery(): void;
}
