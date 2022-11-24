<?php

namespace App\Service\OffsetQuery;

interface QueryInterface
{
    // set query from last valid post
    public function set(string $query): void;

    // get query from last remembered post
    public function get(): string;

    // set query to empty string
    public function reset(): void;
}
